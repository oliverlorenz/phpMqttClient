<?php
/**
 * @author Oliver Lorenz
 * @since 2015-05-10
 * Time: 16:34
 */

class DisconnectTest extends PHPUnit_Framework_TestCase {

    public function testPublishStandard()
    {
        $version = new \oliverlorenz\reactphpmqtt\protocol\Version4();
        $packet = new \oliverlorenz\reactphpmqtt\packet\Disconnect($version);
        $this->assertEquals(
            \oliverlorenz\reactphpmqtt\packet\Disconnect::getControlPacketType(),
            14
        );
    }

}