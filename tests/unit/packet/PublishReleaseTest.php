<?php
/**
 * @author Oliver Lorenz
 * @since 2015-05-11
 * Time: 23:33
 */

class PublishReleaseTest extends PHPUnit_Framework_TestCase {

    public function testGetControlPacketType()
    {
        $this->assertEquals(
            \oliverlorenz\reactphpmqtt\packet\PublishRelease::getControlPacketType(),
            6
        );
    }

}