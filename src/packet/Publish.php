<?php
/**
 * @author Oliver Lorenz
 * @since 2015-04-24
 * Time: 01:22
 */

namespace oliverlorenz\reactphpmqtt\packet;

/**
 * A PUBLISH Control Packet is sent from a Client to a Server or from
 * Server to a Client to transport an Application Message.
 */
class Publish extends ControlPacket
{
    const EVENT = 'PUBLISH';

    private $messageId;

    private $topic = '';

    private $qos = 0;

    private $dup = false;

    private $retain = false;

    /**
     * @param string $topicName The Topic Name identifies the information channel to which payload data is published.
     */
    public function __construct($topicName)
    {
        $this->topic = $topicName;
    }

    public static function getControlPacketType()
    {
        return ControlPacketType::PUBLISH;
    }

    public static function parse($rawInput)
    {
        static::checkRawInputValidControlPackageType($rawInput);

        //TODO 3.3.2.2 Packet Identifier not yet supported
        $topic = static::getPayloadLengthPrefixFieldInRawInput(2, $rawInput);

        $byte1 = $rawInput{0};
        $retain = ($byte1 & 1) === 1;
        $qos = 0;
        if (($byte1 & 2) === 2) {
            $qos = 1;
        } elseif (($byte1 & 4) === 4) {
            $qos = 2;
        }
        $dup = ($byte1 & 8) === 8;

        $payload = substr(
            $rawInput,
            4 + strlen($topic)
        );

        $packet = new static($topic);
        $packet->setRetain($retain);
        $packet->setQos($qos);
        $packet->setDup($dup);
        $packet->addRawToPayLoad($payload);

        return $packet;
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
