<?php
/**
 * @author Oliver Lorenz
 * @since 2015-05-12
 * Time: 14:35
 */

namespace oliverlorenz\reactphpmqtt\packet;

use PHPUnit_Framework_TestCase;

class UnsubscribeAckTest extends PHPUnit_Framework_TestCase {

    public function testUnsubscribeControlPacketTypeIsEleven()
    {
        $this->assertEquals(11, UnsubscribeAck::getControlPacketType());
    }
}
