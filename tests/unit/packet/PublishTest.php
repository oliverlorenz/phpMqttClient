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
            \oliverlorenz\reactphpmqtt\packet\Publish::getControlPacketType(),
            3
        );
    }

    public function testSetTopic()
    {
        $topic = 'topictest';

        $version = new \oliverlorenz\reactphpmqtt\protocol\Version4();
        $packet = new \oliverlorenz\reactphpmqtt\packet\Publish($version);
        $packet->setTopic($topic);

        $reflection = new ReflectionClass('\oliverlorenz\reactphpmqtt\packet\Publish');
        $property = $reflection->getProperty('topic');
        $property->setAccessible(true);

        $this->assertEquals(
            $property->getValue($packet),
            $topic
        );
    }

    public function testSetTopicReturn()
    {
        $topic = 'topictest';

        $version = new \oliverlorenz\reactphpmqtt\protocol\Version4();
        $packet = new \oliverlorenz\reactphpmqtt\packet\Publish($version);
        $return = $packet->setTopic($topic);
        $this->assertInstanceOf('\oliverlorenz\reactphpmqtt\packet\Publish', $return);
    }

    public function testSetMessageId()
    {
        $messageId = 1;

        $version = new \oliverlorenz\reactphpmqtt\protocol\Version4();
        $packet = new \oliverlorenz\reactphpmqtt\packet\Publish($version);
        $packet->setMessageId($messageId);

        $reflection = new ReflectionClass('\oliverlorenz\reactphpmqtt\packet\Publish');
        $property = $reflection->getProperty('messageId');
        $property->setAccessible(true);

        $this->assertEquals(
            $property->getValue($packet),
            $messageId
        );
    }

    public function testSetMessageIdReturn()
    {
        $messageId = 1;

        $version = new \oliverlorenz\reactphpmqtt\protocol\Version4();
        $packet = new \oliverlorenz\reactphpmqtt\packet\Publish($version);
        $return = $packet->setMessageId($messageId);
        $this->assertInstanceOf('\oliverlorenz\reactphpmqtt\packet\Publish', $return);
    }
}