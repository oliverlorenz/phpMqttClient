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
use oliverlorenz\reactphpmqtt\packet\PingResponse;
use oliverlorenz\reactphpmqtt\packet\Publish;
use oliverlorenz\reactphpmqtt\packet\PublishReceived;
use oliverlorenz\reactphpmqtt\packet\PublishRelease;
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
    private $socketConnector;
    private $version;
//    protected $isConnected = false;
//    /** @var Stream|null $stream */
//    protected $stream;
//    protected $onConnected;

//    protected $pingedBack = null;
    private $messageCounter = 1;

    public function __construct(LoopInterface $loop, Resolver $resolver, Version $version)
    {
        $this->version = $version;
        $this->socketConnector = new \React\SocketClient\Connector($loop, $resolver);
        $this->loop = $loop;
    }

//    protected $onPublishReceived;

//    /**
//     * @return Stream
//     */
//    public function getStream()
//    {
//        return $this->stream;
//    }

//    /**
//     * @return bool
//     */
//    public function isConnected()
//    {
//        return !is_null($this->stream);
//    }

//    public function onConnected(callable $function)
//    {
//        $this->onConnected = $function;
//    }

//    public function onPublishReceived(callable $function)
//    {
//        $this->onPublishReceived = $function;
//    }

//    public function __destruct()
//    {
//        $this->disconnect();
//    }

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
            ->then(function(Stream $stream) {
                return $this->keepAlive($stream);
            });
    }

    private function listenForPackets(Stream $stream)
    {
        $stream->on('data', function ($rawData) use($stream) {
            $messages = $this->splitMessage($rawData);
            foreach ($messages as $data) {
                try {
                    $message = Factory::getByMessage($this->version, $data);
                    echo "received:\t" . get_class($message) . "\n";
                    // echo MessageHelper::getReadableByRawString($data);
                    if ($message instanceof ConnectionAck) {
                        $stream->emit('CONNECTION_ACK', array($message));
                    } elseif ($message instanceof PingResponse) {
                        $stream->emit('PING_RESPONSE', array($message));
                    } elseif ($message instanceof Publish) {
                        $stream->emit('PUBLISH', array($message));
                    } elseif ($message instanceof PublishReceived) {
                        $stream->emit('PUBLISH_RECEIVED', array($message));
                    } elseif ($message instanceof PublishRelease) {
                        $stream->emit('PUBLISH_RELEASE', array($message));
                    } elseif ($message instanceof UnsubscribeAck) {
                        $stream->emit('UNSUBSCRIBE_ACK', array($message));
                    } elseif ($message instanceof SubscribeAck) {
                        $stream->emit('SUBSCRIBE_ACK', array($message));
                    }
                } catch (\InvalidArgumentException $ex) {

                }
            }
        });

        $deferred = new Deferred();
        $stream->on('CONNECTION_ACK', function($message) use ($stream, $deferred) {
            $deferred->resolve($stream);
        });

        return $deferred->promise();
    }

    private function keepAlive(Stream $stream)
    {
        $this->getLoop()->addPeriodicTimer(10, function(Timer $timer) use ($stream) {
            $packet = new PingRequest($this->version);
            $this->sendPacketToStream($stream, $packet);
        });

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
            $options->willRetain
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
//        return $this->sendToStream($stream, $message);
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
        $stream->on('SUBSCRIBE_ACK', function($message) use ($stream, $deferred) {
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
        $stream->on('UNSUBSCRIBE_ACK', function($message) use ($stream, $deferred) {
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

//    private function registerSignalHandler()
//    {
//        /*
//        pcntl_signal(SIGTERM, array($this, "processSignal"));
//        pcntl_signal(SIGHUP,  array($this, "processSignal"));
//        pcntl_signal(SIGINT, array($this, "processSignal"));
//        */
//    }

    private function splitMessage($data)
    {
        $messages = array();
        while(true) {
            if (isset($data{1})) {
                $length = ord($data{1});
                $messages[] = substr($data, 0, $length + 2);
                $data = substr($data, $length + 2);
            }

            if (empty($data)) {
                break;
            }
        }

        return $messages;
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
