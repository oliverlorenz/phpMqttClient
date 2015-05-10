<?php
/**
 * @author Oliver Lorenz
 * @since 2015-05-08
 * Time: 18:56
 */

use \oliverlorenz\reactphpmqtt\packet\MessageHelper;

class ConnectTest extends PHPUnit_Framework_TestCase {

    public function testGetControlPacketType()
    {
        $version = new \oliverlorenz\reactphpmqtt\protocol\Version4();
        $packet = new \oliverlorenz\reactphpmqtt\packet\Connect($version);
        $this->assertEquals(
            \oliverlorenz\reactphpmqtt\packet\Connect::getControlPacketType(),
            1
        );
    }

    public function testGetHeaderTestFixedHeader()
    {
        $version = new \oliverlorenz\reactphpmqtt\protocol\Version4();
        $packet = new \oliverlorenz\reactphpmqtt\packet\Connect($version, 'clientid');
        $this->assertEquals(
            substr($packet->get(), 0, 2),
            chr(1 << 4) . chr(20)
        );
    }

    public function testGetHeaderTestVariableHeaderWithoutConnectFlags()
    {
        $version = new \oliverlorenz\reactphpmqtt\protocol\Version4();
        $packet = new \oliverlorenz\reactphpmqtt\packet\Connect($version);
        $this->assertEquals(
            substr($packet->get(), 2, 10),
            chr(0) .    // byte 1
            chr(4) .    // byte 2
            'MQTT' .    // byte 3,4,5,6
            chr(4) .    // byte 7
            chr(1) .    // byte 8
            chr(0) .    // byte 9
            chr(10)     // byte 10
        );
    }

    public function testGetHeaderTestPayloadClientId()
    {
        $version = new \oliverlorenz\reactphpmqtt\protocol\Version4();
        $packet = new \oliverlorenz\reactphpmqtt\packet\Connect($version, 'clientid');
        $this->assertEquals(
            substr($packet->get(), 12),
            chr(0) .    // byte 1
            chr(8) .    // byte 2
            'clientid'
        );
    }

}