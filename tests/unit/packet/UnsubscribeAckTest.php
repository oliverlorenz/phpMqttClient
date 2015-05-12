<?php
/**
 * @author Oliver Lorenz
 * @since 2015-05-12
 * Time: 14:35
 */

class UnsubscribeAckTest extends PHPUnit_Framework_TestCase {

    public function testPublishStandard()
    {
        $version = new \oliverlorenz\reactphpmqtt\protocol\Version4();
        $packet = new \oliverlorenz\reactphpmqtt\packet\UnsubscribeAck($version);
        $this->assertEquals(
            \oliverlorenz\reactphpmqtt\packet\UnsubscribeAck::getControlPacketType(),
            11
        );
    }
}