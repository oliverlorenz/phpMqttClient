<?php
/**
 * @author Oliver Lorenz
 * @since 2015-04-24
 * Time: 19:40
 */

namespace oliverlorenz\reactphpmqtt\packet;

class Factory {

    /**
     * @param $version
     * @param $input
     * @throws \InvalidArgumentException
     * @return ControlPacket
     */
    public static function getByMessage($version, $input)
    {
        if(empty($input)) {
            throw new \InvalidArgumentException('input empty');
        }
        $packetControlType = ord($input{0}) >> 4;
        switch ($packetControlType) {
            case ConnectionAck::getControlPacketType():
                return ConnectionAck::parse($version, $input);
            case PingResponse::getControlPacketType():
                return new PingResponse($version, $input);
            case SubscribeAck::getControlPacketType():
                return new SubscribeAck($version, $input);
            case Publish::getControlPacketType():
                return Publish::parse($version, $input);
            case PublishComplete::getControlPacketType():
                return PublishComplete::parse($version, $input);
            case PublishRelease::getControlPacketType():
                return PublishRelease::parse($version, $input);
            case PublishReceived::getControlPacketType():
                return PublishReceived::parse($version, $input);
        }

        throw new \InvalidArgumentException('got message with control packet type ' . $packetControlType);
    }

}
