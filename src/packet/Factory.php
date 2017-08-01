<?php
/**
 * @author Oliver Lorenz
 * @since 2015-04-24
 * Time: 19:40
 */

namespace oliverlorenz\reactphpmqtt\packet;

use oliverlorenz\reactphpmqtt\protocol\Violation as ProtocolViolation;

class Factory
{
    /**
     * @param string $remainingData
     * @throws ProtocolViolation
     * @return ConnectionAck|PingResponse|SubscribeAck|Publish|PublishComplete|PublishRelease|PublishReceived|void
     */
    public static function getNextPacket($remainingData)
    {
        while(isset($remainingData{1})) {
            $remainingLength = ord($remainingData{1});
            $packetLength = 2 + $remainingLength;
            $nextPacketData = substr($remainingData, 0, $packetLength);
            $remainingData = substr($remainingData, $packetLength);

            yield self::getByMessage($nextPacketData);
        }
    }

    private static function getByMessage($input)
    {
        $controlPacketType = ord($input{0}) >> 4;

        switch ($controlPacketType) {
            case ConnectionAck::getControlPacketType():
                return ConnectionAck::parse($input);

            case PingResponse::getControlPacketType():
                return PingResponse::parse($input);

            case SubscribeAck::getControlPacketType():
                return SubscribeAck::parse($input);

            case Publish::getControlPacketType():
                return Publish::parse($input);

            case PublishComplete::getControlPacketType():
                return PublishComplete::parse($input);

            case PublishRelease::getControlPacketType():
                return PublishRelease::parse($input);

            case PublishReceived::getControlPacketType():
                return PublishReceived::parse($input);
        }

        throw new ProtocolViolation('Unexpected packet type: ' . $controlPacketType);
    }
}
