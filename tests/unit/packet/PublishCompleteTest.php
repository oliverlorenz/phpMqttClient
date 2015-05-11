<?php
/**
 * @author Oliver Lorenz
 * @since 2015-05-11
 * Time: 23:30
 */

class PublishCompleteTest extends PHPUnit_Framework_TestCase {

    public function testGetControlPacketType()
    {
        $this->assertEquals(
            \oliverlorenz\reactphpmqtt\packet\PublishComplete::getControlPacketType(),
            7
        );
    }

}