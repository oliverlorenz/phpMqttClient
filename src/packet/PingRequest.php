<?php
/**
 * @author Oliver Lorenz
 * @since 2015-05-05
 * Time: 19:03
 */

namespace oliverlorenz\reactphpmqtt\packet;

use oliverlorenz\reactphpmqtt\protocol\Version;

class PingRequest extends ControlPacket {

    protected $useVariableHeader = true;
    protected $containsPacketIdentifierFiled = false;

    public static function getControlPacketType()
    {
        return ControlPacketType::PINGREQ;
    }
}