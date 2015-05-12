<?php
/**
 * @author Oliver Lorenz
 * @since 2015-05-12
 * Time: 14:34
 */

class UnsubscribeTest extends PHPUnit_Framework_TestCase {

    public function testPublishStandard()
    {
        $version = new \oliverlorenz\reactphpmqtt\protocol\Version4();
        $packet = new \oliverlorenz\reactphpmqtt\packet\Unsubscribe($version);
        $this->assertEquals(
            \oliverlorenz\reactphpmqtt\packet\Unsubscribe::getControlPacketType(),
            10
        );
    }
}