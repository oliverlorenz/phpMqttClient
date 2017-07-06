<?php
/**
 * @author Oliver Lorenz
 * @since 2015-04-24
 * Time: 19:40
 */

namespace oliverlorenz\reactphpmqtt\packet;

use oliverlorenz\reactphpmqtt\protocol\Version;

/**
 * The CONNACK Packet is the packet sent by the Server in response to
 * a CONNECT Packet received from a Client.
 */
class ConnectionAck extends ControlPacket {

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
