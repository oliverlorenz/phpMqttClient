<?php
/**
 * @author Oliver Lorenz
 * @since 2015-05-08
 * Time: 17:09
 */

namespace oliverlorenz\reactphpmqtt\packet;


class SubscribeAck extends ControlPacket {

    protected $useVariableHeader = true;
    protected $containsPacketIdentifierFiled = true;

    public static function getControlPacketType()
    {
        return ControlPacketType::SUBACK;
    }

}