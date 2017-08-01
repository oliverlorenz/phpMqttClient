<?php
/**
 * @author Oliver Lorenz
 * @since 2015-05-10
 * Time: 11:27
 */

namespace oliverlorenz\reactphpmqtt\packet;

use PHPUnit_Framework_TestCase as TestCase;

class SubscribeAckTest extends TestCase {

    public function testSubscribeAckControlPacketTypeIsNine()
    {
        $this->assertEquals(9, SubscribeAck::getControlPacketType());
    }

    public function testGetHeaderTestFixedHeader()
    {
        $packet = new SubscribeAck();

        $this->assertEquals(
            substr($packet->get(), 0, 2),
            chr(9 << 4) . chr(0)
        );
    }
}
