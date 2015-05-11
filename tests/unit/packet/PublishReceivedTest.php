<?php
/**
 * @author Oliver Lorenz
 * @since 2015-05-11
 * Time: 23:31
 */

class PublishReceivedTest extends PHPUnit_Framework_TestCase {

    public function testGetControlPacketType()
    {
        $this->assertEquals(
            \oliverlorenz\reactphpmqtt\packet\PublishReceived::getControlPacketType(),
            5
        );
    }

}