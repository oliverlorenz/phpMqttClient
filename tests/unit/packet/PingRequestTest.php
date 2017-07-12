<?php
/**
 * @author Oliver Lorenz
 * @since 2015-05-10
 * Time: 11:05
 */

namespace oliverlorenz\reactphpmqtt\packet;

use PHPUnit_Framework_TestCase;

class PingRequestTest extends PHPUnit_Framework_TestCase {

    public function testPingRequestControlPacketTypeIsTwelve()
    {
        $this->assertEquals(12, PingRequest::getControlPacketType());
    }

    public function testGetHeaderTestFixedHeader()
    {
        $version = new \oliverlorenz\reactphpmqtt\protocol\Version4();
        $packet = new PingRequest($version);

        $this->assertEquals(
            substr($packet->get(), 0, 2),
            chr(12 << 4) . chr(0)
        );
    }
}
