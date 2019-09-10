<?php
/**
 * @author Oliver Lorenz
 * @since 2015-04-24
 * Time: 19:40
 */

namespace oliverlorenz\reactphpmqtt\packet;

use oliverlorenz\reactphpmqtt\protocol\Version;
use oliverlorenz\reactphpmqtt\protocol\Violation as ProtocolViolation;

class Factory
{
    /**
     * @param Version $version
     * @param string $remainingData
     * @throws ProtocolViolation
     * @return ConnectionAck|PingResponse|SubscribeAck|Publish|PublishComplete|PublishRelease|PublishReceived|void
     */
    public static function getNextPacket(Version $version, $remainingData)
    {
        while(isset($remainingData{1})) {
            $byte = 1;
            $packetLength = 0;
            do {
                $digit = ord($remainingData{$byte});
                $packetLength += $digit;
                $byte++;
            } while (($digit & 128) != 0);
            $packetLength += 2;
            $nextPacketData = substr($remainingData, 0, $packetLength);
            $remainingData = substr($remainingData, $packetLength);

            yield self::getByMessage($version, $nextPacketData, $byte);
        }
    }

    private static function getByMessage(Version $version, $input, $topicStart = 2)
    {
        $controlPacketType = ord($input{0}) >> 4;

        switch ($controlPacketType) {
            case ConnectionAck::getControlPacketType():
                return ConnectionAck::parse($version, $input);

            case PingResponse::getControlPacketType():
                return PingResponse::parse($version, $input);

            case SubscribeAck::getControlPacketType():
                return SubscribeAck::parse($version, $input);

            case Publish::getControlPacketType():
                return Publish::parse($version, $input, $topicStart);

            case PublishComplete::getControlPacketType():
                return PublishComplete::parse($version, $input);

            case PublishRelease::getControlPacketType():
                return PublishRelease::parse($version, $input);

            case PublishReceived::getControlPacketType():
                return PublishReceived::parse($version, $input);
        }

        throw new ProtocolViolation('Unexpected packet type: ' . $controlPacketType);
    }
}
