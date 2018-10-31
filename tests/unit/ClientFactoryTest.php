<?php

namespace oliverlorenz\reactphpmqtt;

use oliverlorenz\reactphpmqtt\protocol\Version4;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers \oliverlorenz\reactphpmqtt\ClientFactory
 */
final class ClientFactoryTest extends TestCase
{
    public function testConnectorCanBeCreated()
    {
        $client = ClientFactory::createClient(new Version4(), '8.8.8.8');

        $this->assertInstanceOf(MqttClient::class, $client);
    }

    public function testSecureConnectorCanBeCreated()
    {
        $client = ClientFactory::createSecureClient(new Version4(), '8.8.8.8');

        $this->assertInstanceOf(MqttClient::class, $client);
    }
}
