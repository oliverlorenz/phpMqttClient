<?php
/**
 * @author Oliver Lorenz
 * @since 2015-05-10
 * Time: 11:27
 */

class SubscribeAckTest extends PHPUnit_Framework_TestCase {

    public function testGetControlPacketType()
    {
        $version = new \oliverlorenz\reactphpmqtt\protocol\Version4();
        $packet = new \oliverlorenz\reactphpmqtt\packet\SubscribeAck($version);
        $this->assertEquals(
            \oliverlorenz\reactphpmqtt\packet\SubscribeAck::getControlPacketType(),
            9
        );
    }

    public function testGetHeaderTestFixedHeader()
    {
        $version = new \oliverlorenz\reactphpmqtt\protocol\Version4();
        $packet = new \oliverlorenz\reactphpmqtt\packet\SubscribeAck($version);
        $this->assertEquals(
            substr($packet->get(), 0, 2),
            chr(9 << 4) . chr(0)
        );
    }

}