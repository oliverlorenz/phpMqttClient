<?php
/**
 * @author Oliver Lorenz
 * @since 2015-04-24
 * Time: 01:14
 */

namespace oliverlorenz\reactphpmqtt\packet;

/**
 * The DISCONNECT Packet is the final Control Packet sent from the Client
 * to the Server. It indicates that the Client is disconnecting cleanly.
 */
class Disconnect extends ControlPacket
{
    public static function getControlPacketType()
    {
        return ControlPacketType::DISCONNECT;
    }
}