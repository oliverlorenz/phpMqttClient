<?php
/**
 * @author Oliver Lorenz
 * @since 2015-05-08
 * Time: 17:06
 */

namespace oliverlorenz\reactphpmqtt\packet;

/**
 * A PUBACK Packet is the response to a PUBLISH Packet with QoS level 1.
 */
class PublishAck extends ControlPacket {

    public static function getControlPacketType()
    {
        return ControlPacketType::PUBACK;
    }
}
