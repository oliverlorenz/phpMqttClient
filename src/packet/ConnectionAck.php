<?php
/**
 * @author Oliver Lorenz
 * @since 2015-04-24
 * Time: 19:40
 */

namespace oliverlorenz\reactphpmqtt\packet;

use oliverlorenz\reactphpmqtt\protocol\Version;

class ConnectionAck extends ControlPacket {

    protected $useVariableHeader = false;

    public static function getControlPacketType()
    {
        return ControlPacketType::CONNACK;
    }

    public function __construct(Version $version, $input)
    {
        $this->version = $version;
        $this->input = $input;
    }
}