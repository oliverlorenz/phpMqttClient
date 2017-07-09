<?php
/**
 * @author Oliver Lorenz
 * @since 2015-05-08
 * Time: 17:07
 */

namespace oliverlorenz\reactphpmqtt\packet;

/**
 * A PUBREL Packet is the response to a PUBREC Packet.
 * It is the third packet of the QoS 2 protocol exchange.
 */
class PublishRelease extends ControlPacket {

    public static function getControlPacketType()
    {
        return ControlPacketType::PUBREL;
    }
}
