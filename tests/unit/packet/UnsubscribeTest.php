<?php
/**
 * @author Oliver Lorenz
 * @since 2015-05-12
 * Time: 14:34
 */

namespace oliverlorenz\reactphpmqtt\packet;

use PHPUnit_Framework_TestCase;

class UnsubscribeTest extends PHPUnit_Framework_TestCase {

    public function testUnsubscribeControlPacketTypeIsTen()
    {
        $this->assertEquals(10, Unsubscribe::getControlPacketType());
    }

    public function testUnsubscribe()
    {

    }
}