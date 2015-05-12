<?php
/**
 * @author Oliver Lorenz
 * @since 2015-05-08
 * Time: 17:08
 */

namespace oliverlorenz\reactphpmqtt\packet;


class PublishComplete extends ControlPacket {

    protected $useVariableHeader = true;

    public static function getControlPacketType()
    {
        return ControlPacketType::PUBCOMP;
    }

}