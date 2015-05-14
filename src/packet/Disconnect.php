<?php
/**
 * @author Oliver Lorenz
 * @since 2015-04-24
 * Time: 01:14
 */

namespace oliverlorenz\reactphpmqtt\packet;

use oliverlorenz\reactphpmqtt\protocol\Version;

class Disconnect extends ControlPacket
{
    public static function getControlPacketType()
    {
        return ControlPacketType::DISCONNECT;
    }
}