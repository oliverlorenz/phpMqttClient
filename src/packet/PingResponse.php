<?php
/**
 * @author Oliver Lorenz
 * @since 2015-05-05
 * Time: 19:12
 */

namespace oliverlorenz\reactphpmqtt\packet;

use oliverlorenz\reactphpmqtt\protocol\Version;

class PingResponse extends ControlPacket {

    protected $containsPacketIdentifierFiled = false;

    public static function getControlPacketType()
    {
        return ControlPacketType::PINGRESP;
    }
}