<?php

namespace oliverlorenz\reactphpmqtt\packet;

use oliverlorenz\reactphpmqtt\protocol\Version4;
use PHPUnit_Framework_TestCase;

class ConnectionAckTest extends PHPUnit_Framework_TestCase
{
    public function testPingResponseControlPacketTypeIsThirteen()
    {
        $packetType = ConnectionAck::getControlPacketType();

        $this->assertEquals(2, $packetType);
    }

    public function testExceptionIsThrownForUnexpectedPacketType()
    {
        $input =
            chr(0b10101010) .
            chr(2) .
            chr(0) .
            chr(0);

        $this->setExpectedException(
            'RuntimeException',
            'raw input is not valid for this control packet'
        );

        ConnectionAck::parse(new Version4(), $input);
    }

    public function testPacketCanBeParsed()
    {
        $version = new Version4();
        $expectedPacket = new ConnectionAck($version);

        $input =
            chr(0b00100000) .
            chr(2) .
            chr(0) .
            chr(0);

        $parsedPacket = ConnectionAck::parse($version, $input);

        $this->assertEquals($expectedPacket, $parsedPacket);
    }
}
