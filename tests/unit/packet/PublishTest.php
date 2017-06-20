<?php
/**
 * @author Oliver Lorenz
 * @since 2015-05-10
 * Time: 16:35
 */

class PublishTest extends PHPUnit_Framework_TestCase {

    public function testPublishStandard()
    {
        $this->assertEquals(
            \oliverlorenz\reactphpmqtt\packet\Publish::getControlPacketType(),
            3
        );
    }

    public function testPublishStandarWithQos2()
    {
        $version = new \oliverlorenz\reactphpmqtt\protocol\Version4();
        $packet = new \oliverlorenz\reactphpmqtt\packet\Publish($version);
        $packet->setQos(2);
        $this->assertEquals(
            \oliverlorenz\reactphpmqtt\packet\MessageHelper::getReadableByRawString(
                chr(52) .
                chr(2) .
                chr(0) .
                chr(0) .
                chr(49)
            ),
            \oliverlorenz\reactphpmqtt\packet\MessageHelper::getReadableByRawString($packet->get() . 1)
        );
    }

    public function testPublishStandarWithQos1()
    {
        $version = new \oliverlorenz\reactphpmqtt\protocol\Version4();
        $packet = new \oliverlorenz\reactphpmqtt\packet\Publish($version);
        $packet->setQos(1);
        $this->assertEquals(
            \oliverlorenz\reactphpmqtt\packet\MessageHelper::getReadableByRawString(
                chr(50) .
                chr(2) .
                chr(0) .
                chr(0) .
                chr(49)
            ),
            \oliverlorenz\reactphpmqtt\packet\MessageHelper::getReadableByRawString($packet->get() . 1)
        );
    }

    public function testPublishStandarWithQos0()
    {
        $version = new \oliverlorenz\reactphpmqtt\protocol\Version4();
        $packet = new \oliverlorenz\reactphpmqtt\packet\Publish($version);
        $packet->setQos(0);
        $this->assertEquals(
            \oliverlorenz\reactphpmqtt\packet\MessageHelper::getReadableByRawString(
                chr(48) .
                chr(2) .
                chr(0) .
                chr(0) .
                chr(49)
            ),
            \oliverlorenz\reactphpmqtt\packet\MessageHelper::getReadableByRawString($packet->get() . 1)
        );
    }

    public function testPublishStandarWithDup()
    {
        $version = new \oliverlorenz\reactphpmqtt\protocol\Version4();
        $packet = new \oliverlorenz\reactphpmqtt\packet\Publish($version);
        $packet->setDup(true);
        $this->assertEquals(
            \oliverlorenz\reactphpmqtt\packet\MessageHelper::getReadableByRawString(
                chr(56) .
                chr(2) .
                chr(0) .
                chr(0) .
                chr(49)
            ),
            \oliverlorenz\reactphpmqtt\packet\MessageHelper::getReadableByRawString($packet->get() . 1)
        );
    }

    public function testPublishStandarWithRetain()
    {
        $version = new \oliverlorenz\reactphpmqtt\protocol\Version4();
        $packet = new \oliverlorenz\reactphpmqtt\packet\Publish($version);
        $packet->setRetain(true);
        $this->assertEquals(
            \oliverlorenz\reactphpmqtt\packet\MessageHelper::getReadableByRawString(
                chr(49) .
                chr(2) .
                chr(0) .
                chr(0) .
                chr(49)
            ),
            \oliverlorenz\reactphpmqtt\packet\MessageHelper::getReadableByRawString($packet->get() . 1)
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

    public function testSetQos()
    {
        $qos = 2;

        $version = new \oliverlorenz\reactphpmqtt\protocol\Version4();
        $packet = new \oliverlorenz\reactphpmqtt\packet\Publish($version);
        $packet->setQos($qos);

        $reflection = new ReflectionClass('\oliverlorenz\reactphpmqtt\packet\Publish');
        $property = $reflection->getProperty('qos');
        $property->setAccessible(true);

        $this->assertEquals(
            $property->getValue($packet),
            $qos
        );
    }

    public function testSetDup()
    {
        $dup = true;

        $version = new \oliverlorenz\reactphpmqtt\protocol\Version4();
        $packet = new \oliverlorenz\reactphpmqtt\packet\Publish($version);
        $packet->setDup($dup);

        $reflection = new ReflectionClass('\oliverlorenz\reactphpmqtt\packet\Publish');
        $property = $reflection->getProperty('dup');
        $property->setAccessible(true);

        $this->assertEquals(
            $property->getValue($packet),
            $dup
        );
    }

    public function testSetRetain()
    {
        $retain = true;

        $version = new \oliverlorenz\reactphpmqtt\protocol\Version4();
        $packet = new \oliverlorenz\reactphpmqtt\packet\Publish($version);
        $packet->setRetain($retain);

        $reflection = new ReflectionClass('\oliverlorenz\reactphpmqtt\packet\Publish');
        $property = $reflection->getProperty('retain');
        $property->setAccessible(true);

        $this->assertEquals(
            $property->getValue($packet),
            $retain
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

    public function qosProvider() {
        return array(
            array(0, 48),
            array(1, 50),
            array(2, 52),
        );
    }

    /**
     * @dataProvider qosProvider
     */
    public function testParseWithQos($qos, $bit)
    {
        $input =
            chr($bit) .
//            chr(4) .
            chr(2) .
            chr(0) .
            chr(0) /*.
            chr(0) .
            chr(10)*/
        ;
        $version = new \oliverlorenz\reactphpmqtt\protocol\Version4();
        $parsedPacket = \oliverlorenz\reactphpmqtt\packet\Publish::parse($version, $input);

        $comparisonPacket = new \oliverlorenz\reactphpmqtt\packet\Publish($version);
        $comparisonPacket->setQos($qos);

        $this->assertEquals(
            \oliverlorenz\reactphpmqtt\packet\MessageHelper::getReadableByRawString($parsedPacket->get()),
            \oliverlorenz\reactphpmqtt\packet\MessageHelper::getReadableByRawString($comparisonPacket->get())
        );
    }

    public function testParseWithRetain()
    {
        $input =
            chr(49) .
//            chr(4) .
            chr(2) .
            chr(0) .
            chr(0) /*.
            chr(0) .
            chr(10)*/
        ;
        $version = new \oliverlorenz\reactphpmqtt\protocol\Version4();
        $parsedPacket = \oliverlorenz\reactphpmqtt\packet\Publish::parse($version, $input);

        $comparisonPacket = new \oliverlorenz\reactphpmqtt\packet\Publish($version);
        $comparisonPacket->setRetain(true);

        $this->assertEquals(
            \oliverlorenz\reactphpmqtt\packet\MessageHelper::getReadableByRawString($parsedPacket->get()),
            \oliverlorenz\reactphpmqtt\packet\MessageHelper::getReadableByRawString($comparisonPacket->get())
        );
    }

    public function testParseWithDup()
    {
        $input =
            chr(56) .
//            chr(4) .
            chr(2) .
            chr(0) .
            chr(0) /*.
            chr(0) .
            chr(10)*/
        ;
        $version = new \oliverlorenz\reactphpmqtt\protocol\Version4();
        $parsedPacket = \oliverlorenz\reactphpmqtt\packet\Publish::parse($version, $input);

        $comparisonPacket = new \oliverlorenz\reactphpmqtt\packet\Publish($version);
        $comparisonPacket->setDup(true);

        $this->assertEquals(
            \oliverlorenz\reactphpmqtt\packet\MessageHelper::getReadableByRawString($parsedPacket->get()),
            \oliverlorenz\reactphpmqtt\packet\MessageHelper::getReadableByRawString($comparisonPacket->get())
        );
    }
}