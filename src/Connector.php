<?php
/**
 * @author Oliver Lorenz
 * @since 2015-04-28
 * Time: 18:42
 */

namespace oliverlorenz\reactphpmqtt;

use oliverlorenz\reactphpmqtt\packet\Connect;
use oliverlorenz\reactphpmqtt\packet\ConnectionAck;
use oliverlorenz\reactphpmqtt\packet\ConnectionOptions;
use oliverlorenz\reactphpmqtt\packet\ControlPacket;
use oliverlorenz\reactphpmqtt\packet\Disconnect;
use oliverlorenz\reactphpmqtt\packet\Factory;
use oliverlorenz\reactphpmqtt\packet\MessageHelper;
use oliverlorenz\reactphpmqtt\packet\PingRequest;
use oliverlorenz\reactphpmqtt\packet\Publish;
use oliverlorenz\reactphpmqtt\packet\Subscribe;
use oliverlorenz\reactphpmqtt\packet\SubscribeAck;
use oliverlorenz\reactphpmqtt\packet\Unsubscribe;
use oliverlorenz\reactphpmqtt\packet\UnsubscribeAck;
use oliverlorenz\reactphpmqtt\protocol\Version;
use React\Dns\Resolver\Resolver;
use React\EventLoop\LoopInterface;
use React\EventLoop\Timer\Timer;
use React\Promise\Deferred;
use React\Promise\FulfilledPromise;
use React\Promise\PromiseInterface;
use React\SocketClient\ConnectorInterface;
use React\Stream\Stream;

class Connector implements ConnectorInterface {

    /**
     * @var $loop LoopInterface
     */
    private $loop;
    protected $socketConnector;
    private $version;
    private $messageCounter = 1;

    public function __construct(LoopInterface $loop, Resolver $resolver, Version $version)
    {
        $this->version = $version;
        $this->socketConnector = new \React\SocketClient\Connector($loop, $resolver);
        $this->loop = $loop;
    }

    /**
     * Creates a new connection
     *
     * @param string $host
     * @param int $port [optional]
     * @param ConnectionOptions|null $options [optional]
     *
     * @return PromiseInterface Resolves to a \React\Stream\Stream once a connection has been established
     */
    public function create(
        $host,
        $port = 1883,
        ConnectionOptions $options = null
    ) {
        // Set default connection options, if none provided
        if($options == null) {
            $options = $this->getDefaultConnectionOptions();
        }

        return $this->socketConnector->create($host, $port)
            ->then(function (Stream $stream) use ($options) {
                return $this->connect($stream, $options);
            })
            ->then(function (Stream $stream) {
                return $this->listenForPackets($stream);
            })
            ->then(function(Stream $stream) use ($options) {
                return $this->keepAlive($stream, $options->keepAlive);
            });
    }

    private function listenForPackets(Stream $stream)
    {
        $stream->on('data', function ($rawData) use ($stream) {
            try {
                foreach (Factory::splitMessage($this->version, $rawData) as $packet) {
                    $stream->emit($packet::EVENT, [$packet]);
                    echo "received:\t" . get_class($packet) . PHP_EOL;
                }
            }
            //TODO InvalidPacketException or something
            catch (\InvalidArgumentException $e) {
                //TODO Actually, the spec says to disconnect if you receive invalid data.
                $stream->emit('INVALID', [$e]);
            }
        });

        $deferred = new Deferred();
        $stream->on(ConnectionAck::EVENT, function($message) use ($stream, $deferred) {
            $deferred->resolve($stream);
        });

        return $deferred->promise();
    }

    private function keepAlive(Stream $stream, $keepAlive)
    {
        if($keepAlive > 0) {
            $interval = $keepAlive / 2;

            $this->getLoop()->addPeriodicTimer($interval, function(Timer $timer) use ($stream) {
                $packet = new PingRequest($this->version);
                $this->sendPacketToStream($stream, $packet);
            });
        }

        return new FulfilledPromise($stream);
    }

    /**
     * @return \React\Promise\Promise
     */
    public function connect(Stream $stream, ConnectionOptions $options) {
        $packet = new Connect(
            $this->version,
            $options->username,
            $options->password,
            $options->clientId,
            $options->cleanSession,
            $options->willTopic,
            $options->willMessage,
            $options->willQos,
            $options->willRetain,
            $options->keepAlive
        );
        $message = $packet->get();
        echo MessageHelper::getReadableByRawString($message);

        $deferred = new Deferred();
        if ($stream->write($message)) {
            $deferred->resolve($stream);
        } else {
            $deferred->reject();
        }

        return $deferred->promise();
    }

    private function sendPacketToStream(Stream $stream, ControlPacket $controlPacket)
    {
        echo "send:\t\t" . get_class($controlPacket) . "\n";
        $message = $controlPacket->get();

        return $stream->write($message);
    }

    /**
     * @param Stream $stream
     * @param string $topic
     * @param int $qos
     * @return \React\Promise\Promise
     */
    public function subscribe(Stream $stream, $topic, $qos = 0)
    {
        $packet = new Subscribe($this->version);
        $packet->addSubscription($topic, $qos);
        $this->sendPacketToStream($stream, $packet);

        $deferred = new Deferred();
        $stream->on(SubscribeAck::EVENT, function($message) use ($stream, $deferred) {
            $deferred->resolve($stream);
        });

        return $deferred->promise();
    }

    /**
     * @param Stream $stream
     * @param string $topic
     * @return \React\Promise\Promise
     */
    public function unsubscribe(Stream $stream, $topic)
    {
        $packet = new Unsubscribe($this->version);
        $packet->removeSubscription($topic);
        $this->sendPacketToStream($stream, $packet);

        $deferred = new Deferred();
        $stream->on(UnsubscribeAck::EVENT, function($message) use ($stream, $deferred) {
            $deferred->resolve($stream);
        });

        return $deferred->promise();
    }

    public function disconnect(Stream $stream)
    {
        $packet = new Disconnect($this->version);
        $this->sendPacketToStream($stream, $packet);
        $this->getLoop()->stop();

        return new FulfilledPromise($stream);
    }

    /**
     * @return \React\Promise\Promise
     */
    public function publish(Stream $stream, $topic, $message, $qos = 0, $dup = false, $retain = false)
    {
        $packet = new Publish($this->version);
        $packet->setTopic($topic);
        $packet->setMessageId($this->messageCounter++);
        $packet->setQos($qos);
        $packet->setDup($dup);
        $packet->setRetain($retain);
        $packet->addRawToPayLoad($message);

        $success = $this->sendPacketToStream($stream, $packet);

        $deferred = new Deferred();
        if ($success) {
            $deferred->resolve($stream);
        } else {
            $deferred->reject();
        }

        return $deferred->promise();
    }

    /**
     * @return LoopInterface
     */
    public function getLoop()
    {
        return $this->loop;
    }

    /**
     * Returns default connection options
     *
     * @return ConnectionOptions
     */
    private function getDefaultConnectionOptions()
    {
        return new ConnectionOptions();
    }
}
