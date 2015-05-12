<?php
/**
 * @author Oliver Lorenz
 * @since 2015-05-08
 * Time: 17:10
 */

namespace oliverlorenz\reactphpmqtt\packet;


class Unsubscribe extends ControlPacket {

    protected $useVariableHeader = true;

    public static function getControlPacketType()
    {
        return ControlPacketType::UNSUBSCRIBE;
    }

}