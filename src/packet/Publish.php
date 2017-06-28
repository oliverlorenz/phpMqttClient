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

    protected $topic = '';

    protected $qos = 0;

    protected $dup = false;

    protected $retain = false;

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
//        $packet->setReceiveTimestamp(new \DateTime());
        if (!empty($rawInput{0})) {
            $packet->setRetain(($rawInput{0} & 1) === 1);
            $packet->setDup(($rawInput{0} & 8) === 8);
            if (($rawInput{0} & 2) === 2) {
                $packet->setQos(1);
            } elseif (($rawInput{0} & 4) === 4) {
                $packet->setQos(2);
            }
        }
        $packet->payload = substr(
            $rawInput,
            4 + strlen($topic)
        );

        return $packet;
    }

    /**
     * @param $topic
     * @return $this
     */
    public function setTopic($topic)
    {
        $this->topic = $topic;
        return $this;
    }

    /**
     * @param $messageId
     * @return $this
     */
    public function setMessageId($messageId)
    {
        $this->messageId = $messageId;
        return $this;
    }

    /**
     * @param int $qos 0,1,2
     * @return $this
     */
    public function setQos($qos)
    {
        $this->qos = $qos;
        return $this;
    }

    /**
     * @param bool $dup
     * @return $this
     */
    public function setDup($dup)
    {
        $this->dup = $dup;
        return $this;
    }

    /**
     * @param bool $retain
     * @return $this
     */
    public function setRetain($retain)
    {
        $this->retain = $retain;
        return $this;
    }

    /**
     * @return string
     */
    public function getTopic()
    {
        return $this->topic;
    }

    /**
     * @return int
     */
    public function getQos()
    {
        return $this->qos;
    }


    /**
     * @return string
     */
    protected function getVariableHeader()
    {
        return $this->getLengthPrefixField($this->topic);
    }

//    /**
//     * @param \DateTime $dateTime
//     */
//    private function setReceiveTimestamp($dateTime)
//    {
//        $this->receiveTimestamp = $dateTime;
//    }

    protected function addReservedBitsToFixedHeaderControlPacketType($byte1)
    {
        $qosByte = 0;
        if ($this->qos === 1) {
            $qosByte = 1;
        } else if ($this->qos === 2) {
            $qosByte = 2;
        }
        $byte1 += $qosByte << 1;

        if ($this->dup) {
            $byte1 += 8;
        }

        if ($this->retain) {
            $byte1 += 1;
        }

        return $byte1;
    }


}