<?php
/**
 * @author Oliver Lorenz
 * @since 2015-05-10
 * Time: 11:05
 */

class PingRequestTest extends PHPUnit_Framework_TestCase {

    public function testGetControlPacketType()
    {
        $version = new \oliverlorenz\reactphpmqtt\protocol\Version4();
        $packet = new \oliverlorenz\reactphpmqtt\packet\PingRequest($version);
        $this->assertEquals(
            \oliverlorenz\reactphpmqtt\packet\PingRequest::getControlPacketType(),
            12
        );
    }

    public function testGetHeaderTestFixedHeader()
    {
        $version = new \oliverlorenz\reactphpmqtt\protocol\Version4();
        $packet = new \oliverlorenz\reactphpmqtt\packet\PingRequest($version);
        $this->assertEquals(
            substr($packet->get(), 0, 2),
            chr(12 << 4) . chr(0)
        );
    }
}