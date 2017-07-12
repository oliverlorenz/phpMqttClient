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
 */
class FactoryTest extends PHPUnit_Framework_TestCase {

    public function testGetByMessageConnectionAck()
    {
        $version = new Version4();
        $packet = Factory::getByMessage(
            $version,
            chr(ControlPacketType::CONNACK << 4)
        );

        $this->assertPacketType('ConnectionAck', $packet);
    }

    public function testGetByMessagePingResponse()
    {
        $version = new Version4();
        $packet = Factory::getByMessage(
            $version,
            chr(ControlPacketType::PINGRESP << 4)
        );

        $this->assertPacketType('PingResponse', $packet);
    }

    public function testGetByMessageSubscribeAck()
    {
        $version = new Version4();
        $packet = Factory::getByMessage(
            $version,
            chr(ControlPacketType::SUBACK << 4)
        );

        $this->assertPacketType('SubscribeAck', $packet);
    }

    public function testGetByMessagePublish()
    {
        $version = new Version4();
        $packet = Factory::getByMessage(
            $version,
            chr(ControlPacketType::PUBLISH << 4) .
            "............"
        );

        $this->assertPacketType('Publish', $packet);
    }

    public function testGetByMessagePublishComplete()
    {
        $version = new Version4();
        $packet = Factory::getByMessage(
            $version,
            chr(ControlPacketType::PUBCOMP << 4)
        );

        $this->assertPacketType('PublishComplete', $packet);
    }

    public function testGetByMessagePublishRelease()
    {
        $version = new Version4();
        $packet = Factory::getByMessage(
            $version,
            chr(ControlPacketType::PUBREL << 4)
        );

        $this->assertPacketType('PublishRelease', $packet);
    }

    public function testGetByMessagePublishReceived()
    {
        $version = new Version4();
        $packet = Factory::getByMessage(
            $version,
            chr(ControlPacketType::PUBREC << 4)
        );

        $this->assertPacketType('PublishReceived', $packet);
    }

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

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetByMessageNoInput()
    {
        $version = new Version4();
        Factory::getByMessage(
            $version,
            ''
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
