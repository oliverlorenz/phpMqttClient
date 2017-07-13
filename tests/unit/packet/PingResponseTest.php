<?php

namespace oliverlorenz\reactphpmqtt\packet;

use oliverlorenz\reactphpmqtt\protocol\Version4;
use PHPUnit_Framework_TestCase;

/**
 * @covers \oliverlorenz\reactphpmqtt\packet\PingResponse
 */
class PingResponseTest extends PHPUnit_Framework_TestCase
{
    public function testPingResponseControlPacketTypeIsThirteen()
    {
        $packetType = PingResponse::getControlPacketType();

        $this->assertEquals(13, $packetType);
    }

    public function testExceptionIsThrownForUnexpectedPacketType()
    {
        $input =
            chr(0b00000000) .
            chr(2) .
            chr(0) .
            chr(0);

        $this->setExpectedException(
            'RuntimeException',
            'raw input is not valid for this control packet'
        );

        PingResponse::parse(new Version4(), $input);
    }

    public function testPacketCanBeParsed()
    {
        $version = new Version4();
        $expectedPacket = new PingResponse($version);

        $input =
            chr(0b11010000) .
            chr(2) .
            chr(0) .
            chr(0);

        $parsedPacket = PingResponse::parse($version, $input);

        $this->assertEquals($expectedPacket, $parsedPacket);
    }
}
