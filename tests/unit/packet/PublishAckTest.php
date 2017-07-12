<?php
/**
 * @author Oliver Lorenz
 * @since 2015-05-11
 * Time: 23:20
 */

namespace oliverlorenz\reactphpmqtt\packet;

use PHPUnit_Framework_TestCase;

class PublishAckTest extends PHPUnit_Framework_TestCase {

    public function testPublishAckControlPacketTypeIsFour()
    {
        $this->assertEquals(4, PublishAck::getControlPacketType());
    }
}
