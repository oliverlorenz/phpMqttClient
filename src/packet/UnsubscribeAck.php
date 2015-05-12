<?php
/**
 * @author Oliver Lorenz
 * @since 2015-05-08
 * Time: 17:10
 */

namespace oliverlorenz\reactphpmqtt\packet;


class UnsubscribeAck extends ControlPacket {

    protected $useVariableHeader = true;
    protected $containsPacketIdentifierFiled = true;

    public static function getControlPacketType()
    {
        return ControlPacketType::UNSUBACK;
    }
}