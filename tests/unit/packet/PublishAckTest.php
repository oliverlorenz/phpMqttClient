<?php
/**
 * @author Oliver Lorenz
 * @since 2015-05-11
 * Time: 23:20
 */

class PublishAckTest extends PHPUnit_Framework_TestCase {

    public function testGetControlPacketType()
    {
        $this->assertEquals(
            \oliverlorenz\reactphpmqtt\packet\PublishAck::getControlPacketType(),
            4
        );
    }
}