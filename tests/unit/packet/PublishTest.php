<?php
/**
 * @author Oliver Lorenz
 * @since 2015-05-10
 * Time: 16:35
 */

class PublishTest extends PHPUnit_Framework_TestCase {

    public function testPublishStandard()
    {
        $version = new \oliverlorenz\reactphpmqtt\protocol\Version4();
        $packet = new \oliverlorenz\reactphpmqtt\packet\Publish($version);
        $this->assertEquals(
            $packet->getControlPacketType(),
            3
        );
    }

    public function testPublishParse()
    {
        // TODO realtest
        $this->assertTrue(true);
    }
}