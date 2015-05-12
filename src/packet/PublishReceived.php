<?php
/**
 * @author Oliver Lorenz
 * @since 2015-05-08
 * Time: 17:07
 */

namespace oliverlorenz\reactphpmqtt\packet;


class PublishReceived {

    protected $useVariableHeader = true;

    public static function getControlPacketType()
    {
        return ControlPacketType::PUBREC;
    }

}