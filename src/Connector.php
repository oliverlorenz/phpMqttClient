<?php
/**
 * @author Oliver Lorenz
 * @since 2015-04-28
 * Time: 18:42
 */

namespace oliverlorenz\reactphpmqtt;

use oliverlorenz\reactphpmqtt\packet\Subscribe;
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
     * @return null|\React\Promise\FulfilledPromise|\React\Promise\Promise|\React\Promise\RejectedPromise|static
     */
    public function create($host, $port)
    {
        return $this->socketConnector->create($host, $port)->then(
            function (Stream $stream) {
                $this->connect($stream, 'testclientuu');
                $stream->on('data', function ($data) use($stream) {
                    try {
                        $message = Factory::getByMessage($this->version,$data);
                        $this->ascii_to_dec($data);
                        if ($message instanceof ConnectionAck) {
                            $stream->emit('CONNECTION_ACK', array('message' => $message));
                        } elseif ($message instanceof PingResponse) {
                            $stream->emit('PING_RESPONSE', array('message' => $message));
                        } elseif ($message instanceof Publish) {
                            $stream->emit('PUBLISH_RECEIVED', array('message' => $message));
                        }
                    } catch (\InvalidArgumentException $ex) {

                    }

                });
                $stream->on('CONNECTION_ACK', function($message) use ($stream) {
                    $this->stream = $stream;
                    $onConnected = $this->onConnected;
                    $onConnected($message);
                });

                $stream->on('PUBLISH_RECEIVED', function($data) use ($stream) {
                    $this->stream = $stream;
                    $onPublishReceived = $this->onPublishReceived;
                    $onPublishReceived($data['message']);
                });

                // alive ping
                $this->socketConnector->getLoop()->addPeriodicTimer(10, function(Timer $timer) use ($stream) {
                    if ($this->isConnected()) {
                        $this->ping($stream);
                    }
                });
            }
        );
    }

    public function ping(Stream $stream)
    {
        $packet = new PingRequest($this->version);
        $message = $packet->get();
        $this->ascii_to_dec($message);
        $stream->write($message);
    }

    public function connect(Stream $stream, $clientId = null)
    {
        $packet = new Connect($this->version, $clientId);
        $message = $packet->get();
        $this->ascii_to_dec($message);
        $stream->write($message);
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
        $this->ascii_to_dec($message);
        $this->getStream()->write($message);
    }

    public function disconnect()
    {
        $packet = new Disconnect($this->version);
        $message = $packet->get();
        $this->ascii_to_dec($message);
        $this->getStream()->write($message);

        $this->socketConnector->getLoop()->stop();
    }

    public function publish($topic, $message)
    {
        $packet = new Publish($this->version);
        $packet->setTopic($topic);
        $packet->setMessageId(1);
        $packet->addRawToPayLoad($message);
        $message = $packet->get();
        $this->ascii_to_dec($message);
        $this->getStream()->write($message);
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
}