<?php
/**
 * @author Oliver Lorenz
 * @since 2015-05-08
 * Time: 17:09
 */

namespace oliverlorenz\reactphpmqtt\packet;

/**
 * A SUBACK Packet is sent by the Server to the Client to confirm receipt
 * and processing of a SUBSCRIBE Packet.
 */
class SubscribeAck extends ControlPacket
{
    const EVENT = 'SUBSCRIBE_ACK';

    public static function getControlPacketType()
    {
        return ControlPacketType::SUBACK;
    }
}
