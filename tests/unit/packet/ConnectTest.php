<?php
/**
 * @author Oliver Lorenz
 * @since 2015-05-08
 * Time: 18:56
 */

namespace oliverlorenz\reactphpmqtt\packet;

use PHPUnit_Framework_TestCase;

class ConnectTest extends PHPUnit_Framework_TestCase {

    public function testConnectControlPacketTypeIsOne()
    {
        $this->assertEquals(1, Connect::getControlPacketType());
    }

    public function testGetHeaderTestFixedHeader()
    {
        $version = new \oliverlorenz\reactphpmqtt\protocol\Version4();
        $packet = new Connect($version, null, null, 'clientid');

        $this->assertEquals(
            MessageHelper::getReadableByRawString(chr(1 << 4) . chr(20)),
            MessageHelper::getReadableByRawString(substr($packet->get(), 0, 2))
        );
    }

    public function testGetHeaderTestVariableHeaderWithoutConnectFlags()
    {
        $version = new \oliverlorenz\reactphpmqtt\protocol\Version4();
        $packet = new Connect($version, null, null, 'clientid', false);

        $this->assertEquals(
            MessageHelper::getReadableByRawString(
                chr(0) .    // byte 1
                chr(4) .    // byte 2
                'MQTT' .    // byte 3,4,5,6
                chr(4) .    // byte 7
                chr(0) .    // byte 8
                chr(0) .    // byte 9
                chr(0)      // byte 10
            ),
            MessageHelper::getReadableByRawString(substr($packet->get(), 2, 10))
        );
    }

    public function testGetHeaderTestVariableHeaderWithConnectFlagsCleanSession()
    {
        $version = new \oliverlorenz\reactphpmqtt\protocol\Version4();
        $packet = new Connect($version);

        $this->assertEquals(
            MessageHelper::getReadableByRawString(
                chr(0) .    // byte 1
                chr(4) .    // byte 2
                'MQTT' .    // byte 3,4,5,6
                chr(4) .    // byte 7
                chr(2) .    // byte 8
                chr(0) .    // byte 9
                chr(0)      // byte 10
            ),
            MessageHelper::getReadableByRawString(substr($packet->get(), 2, 10))
        );
    }

    public function testGetHeaderTestVariableHeaderWithConnectFlagWillFlag()
    {
        $version = new \oliverlorenz\reactphpmqtt\protocol\Version4();
        $packet = new Connect(
            $version, null, null, 'clientId', false, 'willTopic', 'willMessage'
        );

        $this->assertEquals(
            MessageHelper::getReadableByRawString(
                chr(0) .    // byte 1
                chr(4) .    // byte 2
                'MQTT' .    // byte 3,4,5,6
                chr(4) .    // byte 7
                chr(4) .    // byte 8
                chr(0) .    // byte 9
                chr(0)      // byte 10
            ),
            MessageHelper::getReadableByRawString(substr($packet->get(), 2, 10))
        );
    }

    public function testGetHeaderTestVariableHeaderWithConnectFlagWillRetain()
    {
        $version = new \oliverlorenz\reactphpmqtt\protocol\Version4();
        $packet = new Connect(
            $version, null, null, 'clientId', false, null, null, null, true
        );

        $this->assertEquals(
            MessageHelper::getReadableByRawString(
                chr(0) .    // byte 1
                chr(4) .    // byte 2
                'MQTT' .    // byte 3,4,5,6
                chr(4) .    // byte 7
                chr(32) .    // byte 8
                chr(0) .    // byte 9
                chr(0)      // byte 10
            ),
            MessageHelper::getReadableByRawString(substr($packet->get(), 2, 10))
        );
    }

    public function testGetHeaderTestVariableHeaderWithConnectFlagUsername()
    {
        $version = new \oliverlorenz\reactphpmqtt\protocol\Version4();
        $packet = new Connect(
            $version, 'username', null, 'clientId', false, false, null, false
        );

        $this->assertEquals(
            MessageHelper::getReadableByRawString(
                chr(0) .    // byte 1
                chr(4) .    // byte 2
                'MQTT' .    // byte 3,4,5,6
                chr(4) .    // byte 7
                chr(128) .    // byte 8
                chr(0) .    // byte 9
                chr(0)      // byte 10
            ),
            MessageHelper::getReadableByRawString(substr($packet->get(), 2, 10))
        );
    }

    public function testGetHeaderTestVariableHeaderWithConnectFlagPassword()
    {
        $version = new \oliverlorenz\reactphpmqtt\protocol\Version4();
        $packet = new Connect(
            $version, null, 'password', 'clientId', false, false, null, false
        );

        $this->assertEquals(
            MessageHelper::getReadableByRawString(
                chr(0) .    // byte 1
                chr(4) .    // byte 2
                'MQTT' .    // byte 3,4,5,6
                chr(4) .    // byte 7
                chr(64) .    // byte 8
                chr(0) .    // byte 9
                chr(0)      // byte 10
            ),
            MessageHelper::getReadableByRawString(substr($packet->get(), 2, 10))
        );
    }

    public function testGetHeaderTestVariableHeaderWithConnectFlagWillWillQos()
    {
        $version = new \oliverlorenz\reactphpmqtt\protocol\Version4();
        $packet = new Connect(
            $version, null, null, 'clientId', false, null, null, true, null
        );

        $this->assertEquals(
            MessageHelper::getReadableByRawString(
                chr(0) .    // byte 1
                chr(4) .    // byte 2
                'MQTT' .    // byte 3,4,5,6
                chr(4) .    // byte 7
                chr(8) .    // byte 8
                chr(0) .    // byte 9
                chr(0)      // byte 10
            ),
            MessageHelper::getReadableByRawString(substr($packet->get(), 2, 10))
        );
    }

    public function testGetHeaderTestVariableHeaderWithConnectFlagUserNamePasswordCleanSession()
    {
        $version = new \oliverlorenz\reactphpmqtt\protocol\Version4();
        $packet = new Connect(
            $version, 'username', 'password', 'clientId', true, false, null, false
        );

        $this->assertEquals(
            MessageHelper::getReadableByRawString(
                chr(0) .    // byte 1
                chr(4) .    // byte 2
                'MQTT' .    // byte 3,4,5,6
                chr(4) .    // byte 7
                chr(194) .    // byte 8
                chr(0) .    // byte 9
                chr(0)      // byte 10
            ),
            MessageHelper::getReadableByRawString(substr($packet->get(), 2, 10))
        );
    }

    public function testBytesNineAndTenOfVariableHeaderAreKeepAlive()
    {
        $version = new \oliverlorenz\reactphpmqtt\protocol\Version4();
        $packet = new \oliverlorenz\reactphpmqtt\packet\Connect(
            $version, null, null, null, true, null, null, null, null, 999
        );

        $this->assertEquals(
            MessageHelper::getReadableByRawString(
                chr(0) .    // byte 1
                chr(4) .    // byte 2
                'MQTT' .    // byte 3,4,5,6
                chr(4) .    // byte 7
                chr(2) .    // byte 8
                chr(3) .    // byte 9
                chr(231)    // byte 10
            ),
            MessageHelper::getReadableByRawString(substr($packet->get(), 2, 10))
        );
    }

    public function testGetHeaderTestPayloadClientId()
    {
        $version = new \oliverlorenz\reactphpmqtt\protocol\Version4();
        $packet = new Connect($version, null, null, 'clientid');

        $this->assertEquals(
            substr($packet->get(), 12),
            chr(0) .    // byte 1
            chr(8) .    // byte 2
            'clientid'
        );
    }
}
