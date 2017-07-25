<?php
/**
 * @author Oliver Lorenz
 * @since 2015-05-10
 * Time: 10:49
 */

namespace oliverlorenz\reactphpmqtt\packet;

use oliverlorenz\reactphpmqtt\protocol\Version4;
use PHPUnit_Framework_TestCase;

/**
 * @covers \oliverlorenz\reactphpmqtt\packet\Factory
 * @uses \oliverlorenz\reactphpmqtt\packet\ConnectionAck
 * @uses \oliverlorenz\reactphpmqtt\packet\PingResponse
 * @uses \oliverlorenz\reactphpmqtt\packet\Publish
 * @uses \oliverlorenz\reactphpmqtt\packet\PublishComplete
 * @uses \oliverlorenz\reactphpmqtt\packet\PublishReceived
 * @uses \oliverlorenz\reactphpmqtt\packet\PublishRelease
 * @uses \oliverlorenz\reactphpmqtt\packet\SubscribeAck
 */
class FactoryTest extends PHPUnit_Framework_TestCase
{
    public function testStreamDataCanBeSplitIntoMultiplePackets()
    {
        $version = new Version4();
        $incomingPackets = [
            new ConnectionAck($version),
            new PingResponse($version),
            new Publish($version),
            new PublishComplete($version),
            new PublishReceived($version),
            new PublishRelease($version),
            new SubscribeAck($version),
        ];

        $string = array_reduce($incomingPackets, function($string, ControlPacket $packet) {
            return $string . $packet->get();
        }, '');

        $parsedPackets = [];
        foreach (Factory::getNextPacket($version, $string) as $packet) {
            $parsedPackets[] = $packet;
        }

        $this->assertPacketType('ConnectionAck', $parsedPackets[0]);
        $this->assertPacketType('PingResponse', $parsedPackets[1]);
        $this->assertPacketType('Publish', $parsedPackets[2]);
        $this->assertPacketType('PublishComplete', $parsedPackets[3]);
        $this->assertPacketType('PublishReceived', $parsedPackets[4]);
        $this->assertPacketType('PublishRelease', $parsedPackets[5]);
        $this->assertPacketType('SubscribeAck', $parsedPackets[6]);
    }

    public function testAnInvalidPacketTypeCausesAnError()
    {
        $invalidData = chr(512 << 4).'Some invalid data';

        $this->setExpectedException('InvalidArgumentException', 'got message with control packet type 0');

        foreach (Factory::getNextPacket(new Version4(), $invalidData) as $packet) {
            $this->assertFalse(true);
        }
    }

//    public function testGetByMessageConnectionAck()
//    {
//        $version = new Version4();
//        $packet = Factory::getByMessage(
//            $version,
//            chr(ControlPacketType::CONNACK << 4)
//        );
//
//        $this->assertPacketType('ConnectionAck', $packet);
//    }
//
//    public function testGetByMessagePingResponse()
//    {
//        $version = new Version4();
//        $packet = Factory::getByMessage(
//            $version,
//            chr(ControlPacketType::PINGRESP << 4)
//        );
//
//        $this->assertPacketType('PingResponse', $packet);
//    }
//
//    public function testGetByMessageSubscribeAck()
//    {
//        $version = new Version4();
//        $packet = Factory::getByMessage(
//            $version,
//            chr(ControlPacketType::SUBACK << 4)
//        );
//
//        $this->assertPacketType('SubscribeAck', $packet);
//    }
//
//    public function testGetByMessagePublish()
//    {
//        $version = new Version4();
//        $packet = Factory::getByMessage(
//            $version,
//            chr(ControlPacketType::PUBLISH << 4) .
//            "............"
//        );
//
//        $this->assertPacketType('Publish', $packet);
//    }
//
//    public function testGetByMessagePublishComplete()
//    {
//        $version = new Version4();
//        $packet = Factory::getByMessage(
//            $version,
//            chr(ControlPacketType::PUBCOMP << 4)
//        );
//
//        $this->assertPacketType('PublishComplete', $packet);
//    }
//
//    public function testGetByMessagePublishRelease()
//    {
//        $version = new Version4();
//        $packet = Factory::getByMessage(
//            $version,
//            chr(ControlPacketType::PUBREL << 4)
//        );
//
//        $this->assertPacketType('PublishRelease', $packet);
//    }
//
//    public function testGetByMessagePublishReceived()
//    {
//        $version = new Version4();
//        $packet = Factory::getByMessage(
//            $version,
//            chr(ControlPacketType::PUBREC << 4)
//        );
//
//        $this->assertPacketType('PublishReceived', $packet);
//    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetByMessageUnknown()
    {
        $version = new Version4();
        Factory::getByMessage(
            $version,
            chr(512 << 4)
        );
    }

    private function assertPacketType($class, ControlPacket $packet)
    {
        $this->assertInstanceOf(
            sprintf('oliverlorenz\reactphpmqtt\packet\%s', $class),
            $packet
        );
    }
}
