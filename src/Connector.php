<?php
/**
 * @author Oliver Lorenz
 * @since 2015-04-28
 * Time: 18:42
 */

namespace oliverlorenz\reactphpmqtt;

use oliverlorenz\reactphpmqtt\packet\MessageHelper;
use oliverlorenz\reactphpmqtt\packet\PublishAck;
use oliverlorenz\reactphpmqtt\packet\PublishComplete;
use oliverlorenz\reactphpmqtt\packet\PublishReceived;
use oliverlorenz\reactphpmqtt\packet\PublishRelease;
use oliverlorenz\reactphpmqtt\packet\Subscribe;
use oliverlorenz\reactphpmqtt\packet\Unsubscribe;
use oliverlorenz\reactphpmqtt\packet\UnsubscribeAck;
use React\Dns\Resolver\Resolver;
use React\EventLoop\LoopInterface;
use React\EventLoop\Timer\Timer;
use oliverlorenz\reactphpmqtt\packet\Connect;
use oliverlorenz\reactphpmqtt\packet\ConnectionAck;
use oliverlorenz\reactphpmqtt\packet\Disconnect;
use oliverlorenz\reactphpmqtt\packet\Factory;
use oliverlorenz\reactphpmqtt\packet\PingRequest;
use oliverlorenz\reactphpmqtt\packet\PingResponse;
use oliverlorenz\reactphpmqtt\packet\Publish;
use oliverlorenz\reactphpmqtt\protocol\Version;
use React\Stream\Stream;

class Connector implements \React\SocketClient\ConnectorInterface {

    protected $socketConnector;
    protected $version;
    protected $isConnected = false;
    /** @var Stream|null $stream */
    protected $stream;
    protected $onConnected;

    protected $pingedBack = null;
    protected $messageCounter = 1;

    public function __construct(LoopInterface $loop, Resolver $resolver, Version $version)
    {
        $this->version = $version;
        $this->socketConnector = new \React\SocketClient\Connector($loop, $resolver);
    }

    protected $onPublishReceived;

    /**
     * @return Stream
     */
    public function getStream()
    {
        return $this->stream;
    }

    /**
     * @return bool
     */
    public function isConnected()
    {
        return !is_null($this->stream);
    }

    public function onConnected(callable $function)
    {
        $this->onConnected = $function;
    }

    public function onPublishReceived(callable $function)
    {
        $this->onPublishReceived = $function;
    }

    public function __destruct()
    {
        $this->disconnect();
    }

    /**
     * @param $host
     * @param $port
     * @param string|null $username
     * @param string|null $password
     * @param string|null $clientId
     * @param bool|null $cleanSession
     * @param string|null $willTopic
     * @param string|null $willMessage
     * @param int $willQos
     * @param bool|null $willRetain
     * @return null|\React\Promise\FulfilledPromise|\React\Promise\Promise|\React\Promise\RejectedPromise|static
     */
    public function create(
        $host,
        $port,
        $username = null,
        $password = null,
        $clientId = null,
        $cleanSession = true,
        $willTopic = null,
        $willMessage = null,
        $willQos = 0,
        $willRetain = null
    ) {
        return $this->socketConnector->create($host, $port)->then(
            function (Stream $stream) use ($username, $password, $clientId, $cleanSession, $willTopic, $willMessage, $willQos, $willRetain) {
                $this->connect($stream, $username, $password, $clientId, $cleanSession, $willTopic, $willMessage, $willQos, $willRetain);
                $stream->on('data', function ($data) use($stream) {
                    try {
                        $message = Factory::getByMessage($this->version,$data);
                        echo "received:\n";
                        echo MessageHelper::getReadableByRawString($data);
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
                        }
                    } catch (\InvalidArgumentException $ex) {

                    }

                });
                $stream->on('CONNECTION_ACK', function($message) use ($stream) {
                    $this->stream = $stream;
                    $onConnected = $this->onConnected;
                    $onConnected($message);
                    $this->registerSignalHandler();
                });

                $stream->on('PUBLISH', function($message) use ($stream) {
                    /** @var Stream stream */
                    /** @var Publish $message */
                    $this->stream = $stream;
                    if ($message->getQos() == 1) {
                        $pubAck = new PublishAck($this->version);
                        $this->sendToStream($pubAck->get());
                    } elseif ($message->getQos() == 2) {
                        $pubRec = new PublishReceived($this->version);
                        $this->sendToStream($pubRec->get());
                    }
                    if (!is_null($this->onPublishReceived)) {
                        $onPublishReceived = $this->onPublishReceived;
                        $onPublishReceived($message);
                    }
                });

                $stream->on('PUBLISH_RECEIVED', function($message) use ($stream) {
                    /** @var Stream stream */
                    /** @var PublishReceived $message */
                    $pubRel = new PublishRelease($this->version);
                    $this->sendToStream($pubRel->get());
                });

                $stream->on('PUBLISH_RELEASE', function($message) use ($stream) {
                    /** @var Stream stream */
                    /** @var PublishRelease $message */
                    $pubComp = new PublishComplete($this->version);
                    $this->sendToStream($pubComp->get());
                });

                $stream->on('PING_RESPONSE', function($message) use ($stream) {
                    $this->pingedBack = true;
                });

                // alive ping
                $this->socketConnector->getLoop()->addPeriodicTimer(10, function(Timer $timer) use ($stream) {
                    if ($this->isConnected()) {
                   //     if (is_null($this->pingedBack) || $this->pingedBack === true) {
                            $this->ping($stream);
                   //         $this->pingedBack = false;
                   //     } else if ($this->pingedBack === false) {
                   //         $this->disconnect();
                   //         $this->connect($this->stream);
                   //     }
                    }
                });
            }
        );
    }

    public function ping(Stream $stream)
    {
        $packet = new PingRequest($this->version);
        $message = $packet->get();
        $this->sendToStream($message);
    }

    public function connect(
        Stream $stream,
        $username = null,
        $password = null,
        $clientId = null,
        $cleanSession = true,
        $willTopic = null,
        $willMessage = null,
        $willQos = null,
        $willRetain = null
    ) {
        $packet = new Connect(
            $this->version,
            $username,
            $password,
            $clientId,
            $cleanSession,
            $willTopic,
            $willMessage,
            $willQos,
            $willRetain
        );
        $message = $packet->get();
        $this->ascii_to_dec($message);
        $stream->write($message);
    }

    /**
     * @param string $message
     */
    protected function sendToStream($message)
    {
        echo "send:\n";
        echo MessageHelper::getReadableByRawString($message);
        $this->getStream()->write($message);
    }

    /**
     * @param string $topic
     * @param int $qos
     */
    public function subscribe($topic, $qos = 0)
    {
        $packet = new Subscribe($this->version);
        $packet->addSubscription($topic, $qos);
        $message = $packet->get();
        $this->sendToStream($message);
    }

    /**
     * @param string $topic
     */
    public function unsubscribe($topic)
    {
        $packet = new Unsubscribe($this->version);
        $packet->removeSubscription($topic);
        $message = $packet->get();
        $this->sendToStream($message);
    }

    public function disconnect()
    {
        $packet = new Disconnect($this->version);
        $message = $packet->get();
        $this->sendToStream($message);
        $this->getStream()->close();
    }

    public function publish($topic, $message, $qos = 0, $dup = false)
    {
        $packet = new Publish($this->version);
        $packet->setTopic($topic);
        $packet->setMessageId($this->messageCounter++);
        $packet->setQos($qos);
        $packet->setDup($dup);
        $packet->addRawToPayLoad($message);
        $message = $packet->get();
        $this->sendToStream($message);
    }



    function ascii_to_dec($str)
    {
        $str = (string) $str;
        echo "+-----+------+-------+-----+\n";
        echo "| idx | byte | ascii | dec |\n";
        echo "+-----+------+-------+-----+\n";
        for ($i = 0, $j = strlen($str); $i < $j; $i++) {
            echo '| ' . str_pad($i, 4, ' ');
            echo '| ' . str_pad($i+1, 5, ' ');
            echo '| ' . str_pad((ord($str{$i}) > 32 ? $str{$i} : ("(" . ord($str{$i})) . ")"), 6, ' ');
            echo '| ' . str_pad(ord($str{$i}), 4, ' ');
            echo "|\n";
        }
        echo "+-----+------+-------+-----+\n";
    }

    private function registerSignalHandler()
    {
        pcntl_signal(SIGTERM, array($this, "processSignal"));
        pcntl_signal(SIGHUP,  array($this, "processSignal"));
        pcntl_signal(SIGINT, array($this, "processSignal"));
    }

    public function processSignal($signo)
    {
        switch ($signo) {
            case SIGTERM:
            case SIGHUP:
            case SIGINT:
                $this->disconnect();
            default:
                // maybe more?
        }
        exit();
    }
}