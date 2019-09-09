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
use oliverlorenz\reactphpmqtt\packet\PingRequest;
use oliverlorenz\reactphpmqtt\packet\Publish;
use oliverlorenz\reactphpmqtt\packet\Subscribe;
use oliverlorenz\reactphpmqtt\packet\SubscribeAck;
use oliverlorenz\reactphpmqtt\packet\Unsubscribe;
use oliverlorenz\reactphpmqtt\packet\UnsubscribeAck;
use oliverlorenz\reactphpmqtt\protocol\Version;
use oliverlorenz\reactphpmqtt\protocol\Violation as ProtocolViolation;
use React\EventLoop\LoopInterface as Loop;
use React\EventLoop\Timer\Timer;
use React\Promise\Deferred;
use React\Promise\FulfilledPromise;
use React\Promise\PromiseInterface;
use React\Socket\ConnectionInterface as Connection;
use React\Socket\ConnectorInterface as ReactConnector;

class MqttClient
{
    /**
     * @var $loop Loop
     */
    private $loop;
    private $socketConnector;
    private $version;

    private $messageCounter = 1;

    public function __construct(Loop $loop, ReactConnector $connector, Version $version)
    {
        $this->version = $version;
        $this->socketConnector = $connector;
        $this->loop = $loop;
    }

    /**
     * Creates a new connection
     *
     * @param string $uri
     * @param ConnectionOptions|null $options [optional]
     *
     * @return PromiseInterface Resolves to a \React\Stream\Stream once a connection has been established
     */
    public function connect(
        $uri,
        ConnectionOptions $options = null
    ) {
        // Set default connection options, if none provided
        if($options == null) {
            $options = $this->getDefaultConnectionOptions();
        }

        $promise = $this->socketConnector->connect($uri);
        $promise->then(function(Connection $stream) {
            $this->listenForPackets($stream);
        });
        $connection = $promise->then(function(Connection $stream) use ($options) {
            return $this->sendConnectPacket($stream, $options);
        });
        $connection->then(function(Connection $stream) use ($options) {
            return $this->keepAlive($stream, $options->keepAlive);
        });

        return $connection;
    }

    private function listenForPackets(Connection $stream)
    {
        $stream->on('data', function($rawData) use ($stream) {
            try {
                foreach (Factory::getNextPacket($this->version, $rawData) as $packet) {
                    $stream->emit($packet::EVENT, [$packet]);
                }
            }
            catch (ProtocolViolation $e) {
                //TODO Actually, the spec says to disconnect if you receive invalid data.
                $stream->emit('INVALID', [$e]);
            }
        });
//
//        $deferred = new Deferred();
//        $stream->on(ConnectionAck::EVENT, function($message) use ($stream, $deferred) {
//            $deferred->resolve($stream);
//        });
//
//        return $deferred->promise();
    }

    private function keepAlive(Connection $stream, $keepAlive)
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
    public function sendConnectPacket(Connection $stream, ConnectionOptions $options) {
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

        $deferred = new Deferred();
        $stream->on(ConnectionAck::EVENT, function($message) use ($stream, $deferred) {
            $deferred->resolve($stream);
        });

        $stream->write($message);
//        $deferred = new Deferred();
//        if ($stream->write($message)) {
//            $deferred->resolve($stream);
//        } else {
//            $deferred->reject();
//        }

        return $deferred->promise();
    }

    private function sendPacketToStream(Connection $stream, ControlPacket $controlPacket)
    {
        $message = $controlPacket->get();

        return $stream->write($message);
    }

    /**
     * @param Connection $stream
     * @param string $topic
     * @param int $qos
     * @return \React\Promise\Promise
     */
    public function subscribe(Connection $stream, $topic, $qos = 0)
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
     * @param Connection $stream
     * @param string $topic
     * @return \React\Promise\Promise
     */
    public function unsubscribe(Connection $stream, $topic)
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

    public function disconnect(Connection $stream)
    {
        $packet = new Disconnect($this->version);
        $this->sendPacketToStream($stream, $packet);
        $this->getLoop()->stop();

        return new FulfilledPromise($stream);
    }

    /**
     * @return \React\Promise\Promise
     */
    public function publish(Connection $stream, $topic, $message, $qos = 0, $dup = false, $retain = false)
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
     * @return Loop
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
