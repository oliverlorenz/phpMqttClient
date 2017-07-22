<?php
/**
 * @author Oliver Lorenz
 * @since 2015-04-24
 * Time: 19:40
 */

namespace oliverlorenz\reactphpmqtt\packet;

use oliverlorenz\reactphpmqtt\protocol\Version;

class Factory
{
    public static function splitMessage(Version $version, $data)
    {
        while(isset($data{1})) {
            $remainingLength = ord($data{1});
            $packetLength = 2 + $remainingLength;
            $message = substr($data, 0, $packetLength);

            yield self::getByMessage($version, $message);

            $data = substr($data, $packetLength);
        }
    }

    /**
     * @param Version $version
     * @param string $input
     * @throws \InvalidArgumentException
     * @return ConnectionAck|PingResponse|SubscribeAck|Publish|PublishComplete|PublishRelease|PublishReceived
     */
    public static function getByMessage(Version $version, $input)
    {
        $packetControlType = ord($input{0}) >> 4;

        switch ($packetControlType) {
            case ConnectionAck::getControlPacketType():
                return ConnectionAck::parse($version, $input);

            case PingResponse::getControlPacketType():
                return PingResponse::parse($version, $input);

            case SubscribeAck::getControlPacketType():
                return SubscribeAck::parse($version, $input);

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
