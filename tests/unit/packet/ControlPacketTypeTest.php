<?php
/**
 * @author Oliver Lorenz
 * @since 2015-05-10
 * Time: 16:38
 */

use \oliverlorenz\reactphpmqtt\packet\ControlPacketType;

class ControlPacketTypeTest extends PHPUnit_Framework_TestCase {

    public function testConnect()
    {
        $this->assertEquals(
            ControlPacketType::CONNECT,
            1
        );
    }

    public function testConnectAck()
    {
        $this->assertEquals(
            ControlPacketType::CONNACK,
            2
        );
    }

    public function testPublish()
    {
        $this->assertEquals(
            ControlPacketType::PUBLISH,
            3
        );
    }

    public function testPublishAck()
    {
        $this->assertEquals(
            ControlPacketType::PUBACK,
            4
        );
    }

    public function testPublishReceived()
    {
        $this->assertEquals(
            ControlPacketType::PUBREC,
            5
        );
    }

    public function testPublishRelease()
    {
        $this->assertEquals(
            ControlPacketType::PUBREL,
            6
        );
    }

    public function testPublishComp()
    {
        $this->assertEquals(
            ControlPacketType::PUBCOMP,
            7
        );
    }

    public function testSubscribe()
    {
        $this->assertEquals(
            ControlPacketType::SUBSCRIBE,
            8
        );
    }

    public function testSubscribeAck()
    {
        $this->assertEquals(
            ControlPacketType::SUBACK,
            9
        );
    }

    public function testUnsubscribe()
    {
        $this->assertEquals(
            ControlPacketType::UNSUBSCRIBE,
            10
        );
    }

    public function testUnsubscribeAck()
    {
        $this->assertEquals(
            ControlPacketType::UNSUBACK,
            11
        );
    }

    public function testPingRequest()
    {
        $this->assertEquals(
            ControlPacketType::PINGREQ,
            12
        );
    }

    public function testPingResponse()
    {
        $this->assertEquals(
            ControlPacketType::PINGRESP,
            13
        );
    }

    public function testDisconnect()
    {
        $this->assertEquals(
            ControlPacketType::DISCONNECT,
            14
        );
    }



}