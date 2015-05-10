<?php
/**
 * @author Oliver Lorenz
 * @since 2015-05-10
 * Time: 10:49
 */

use \oliverlorenz\reactphpmqtt\packet\Factory;
use \oliverlorenz\reactphpmqtt\protocol\Version4;
use \oliverlorenz\reactphpmqtt\packet\ControlPacketType;

class FactoryTest extends PHPUnit_Framework_TestCase {

    /**
     * @expectedException RuntimeException
     */
    public function testGetByMessageUnknown()
    {
        $version = new Version4();
        $message = Factory::getByMessage(
            $version,
            chr(512 << 4)
        );
    }

    public function testGetByMessageConnectionAck()
    {
        $version = new Version4();
        $message = Factory::getByMessage(
            $version,
            chr(ControlPacketType::CONNACK << 4)
        );
        $this->assertInstanceOf('\oliverlorenz\reactphpmqtt\packet\ConnectionAck', $message);
    }

    public function testGetByMessagePingResponse()
    {
        $version = new Version4();
        $message = Factory::getByMessage(
            $version,
            chr(ControlPacketType::PINGRESP << 4)
        );
        $this->assertInstanceOf('\oliverlorenz\reactphpmqtt\packet\PingResponse', $message);
    }

    public function testGetByMessageSubscribeAck()
    {
        $version = new Version4();
        $message = Factory::getByMessage(
            $version,
            chr(ControlPacketType::SUBACK << 4)
        );
        $this->assertInstanceOf('\oliverlorenz\reactphpmqtt\packet\SubscribeAck', $message);
    }


}