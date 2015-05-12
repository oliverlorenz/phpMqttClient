<?php
/**
 * @author Oliver Lorenz
 * @since 2015-05-08
 * Time: 17:06
 */

namespace oliverlorenz\reactphpmqtt\packet;


class PublishAck extends ControlPacket {

    protected $useVariableHeader = true;

    public static function getControlPacketType()
    {
        return ControlPacketType::PUBACK;
    }

}