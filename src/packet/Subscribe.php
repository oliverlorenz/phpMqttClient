<?php
/**
 * @author Oliver Lorenz
 * @since 2015-05-06
 * Time: 21:10
 */

namespace oliverlorenz\reactphpmqtt\packet;

/**
 * The SUBSCRIBE Packet is sent from the Client to the Server to create
 * one or more Subscriptions.
 */
class Subscribe extends ControlPacket {

    protected $topicFilters = array();

    public static function getControlPacketType()
    {
        return ControlPacketType::SUBSCRIBE;
    }

    protected function addReservedBitsToFixedHeaderControlPacketType($byte1)
    {
        return $byte1 + 2;
    }

    /**
     * @return string
     */
    protected function getVariableHeader()
    {
        return chr(0)
             . chr(10)
        ;
    }

    /**
     * @param string $topic
     * @param int $qos
     */
    public function addSubscription($topic, $qos = 0)
    {
        $this->payload .= $this->getLengthPrefixField($topic);
        $this->payload .= chr($qos);
    }
}
