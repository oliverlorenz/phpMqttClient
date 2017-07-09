<?php
/**
 * @author Oliver Lorenz
 * @since 2015-05-08
 * Time: 17:10
 */

namespace oliverlorenz\reactphpmqtt\packet;

/**
 * An UNSUBSCRIBE Packet is sent by the Client to the Server, to
 * unsubscribe from topics.
 */
class Unsubscribe extends ControlPacket {

    public static function getControlPacketType()
    {
        return ControlPacketType::UNSUBSCRIBE;
    }

    /**
     * @param string $topic
     */
    public function removeSubscription($topic)
    {
        $this->payload .= $this->getLengthPrefixField($topic);
    }
}
