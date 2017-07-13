<?php
/**
 * @author Oliver Lorenz
 * @since 2015-05-10
 * Time: 16:34
 */

namespace oliverlorenz\reactphpmqtt\packet;

use PHPUnit_Framework_TestCase;

class DisconnectTest extends PHPUnit_Framework_TestCase {

    public function testDisconnectControlPacketTypeIsFourteen()
    {
        $this->assertEquals(14, Disconnect::getControlPacketType());
    }
}
