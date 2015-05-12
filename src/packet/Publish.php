<?php
/**
 * @author Oliver Lorenz
 * @since 2015-04-24
 * Time: 01:22
 */

namespace oliverlorenz\reactphpmqtt\packet;

use oliverlorenz\reactphpmqtt\protocol\Version;

class Publish extends ControlPacket {

    protected $messageId;

    /** @var string $message */
    protected $message = null;

    protected $topic;

    protected $useVariableHeader = true;

    /** @var null|\DateTime $receiveTimestamp */
    protected $receiveTimestamp = null;

    public static function getControlPacketType()
    {
        return ControlPacketType::PUBLISH;
    }

    public function __construct(Version $version)
    {
        parent::__construct($version);
    }

    public static function parse(Version $version, $rawInput)
    {
        /** @var Publish $packet */
        $packet = parent::parse($version, $rawInput);
        $topic = static::getPayloadLengthPrefixFieldInRawInput(2, $rawInput);
        $packet->setTopic($topic);
        $packet->setReceiveTimestamp(new \DateTime());
        $packet->setMessage(
            substr(
                $rawInput,
                4 + strlen($topic)
            )
        );
        return $packet;
    }


    public function setTopic($topic)
    {
        $this->topic = $topic;
        return $this;
    }

    public function setMessageId($messageId)
    {
        $this->messageId = $messageId;
        return $this;
    }

    /**
     * @return string
     */
    protected function getVariableHeader()
    {
        return $this->getLengthPrefixField($this->topic)
        // keep alive
        . chr(0x00)
        . chr(0x0a)
            ;
    }

    /**
     * @param \DateTime $dateTime
     */
    private function setReceiveTimestamp($dateTime)
    {
        $this->receiveTimestamp = $dateTime;
    }

    /**
     * @param string $message
     */
    private function setMessage($message)
    {
        $this->message = $message;
    }
}