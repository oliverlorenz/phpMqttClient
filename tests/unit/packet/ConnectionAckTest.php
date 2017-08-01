<?php

namespace oliverlorenz\reactphpmqtt\packet;

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

        ConnectionAck::parse($input);
    }

    public function testPacketCanBeParsed()
    {
        $expectedPacket = new ConnectionAck();

        $input =
            chr(0b00100000) .
            chr(2) .
            chr(0) .
            chr(0);

        $parsedPacket = ConnectionAck::parse($input);

        $this->assertEquals($expectedPacket, $parsedPacket);
    }
}
