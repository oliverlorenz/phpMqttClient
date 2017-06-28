<?php
/**
 * @author Oliver Lorenz
 * @since 2015-05-10
 * Time: 16:35
 */

use oliverlorenz\reactphpmqtt\packet\MessageHelper;
use oliverlorenz\reactphpmqtt\packet\Publish;
use oliverlorenz\reactphpmqtt\protocol\Version4;

class PublishTest extends PHPUnit_Framework_TestCase {

    public function testPublishStandard()
    {
        $this->assertEquals(3, Publish::getControlPacketType());
    }

    public function testPublishStandardWithQos2()
    {
        $packet = new Publish(new Version4());
        $packet->setQos(2);

        $expected =
            chr(0b00110100) .
            chr(2) .
            chr(0) .
            chr(0) .
            chr(49);

        $this->assertSerialisedPacketEquals($expected, $packet->get() . 1);
    }

    public function testPublishStandardWithQos1()
    {
        $packet = new Publish(new Version4());
        $packet->setQos(1);

        $expected =
            chr(0b00110010) .
            chr(2) .
            chr(0) .
            chr(0) .
            chr(49);

        $this->assertSerialisedPacketEquals($expected, $packet->get() . 1);
    }

    public function testPublishStandardWithQos0()
    {
        $packet = new Publish(new Version4());
        $packet->setQos(0);

        $expected =
            chr(0b00110000) .
            chr(2) .
            chr(0) .
            chr(0) .
            chr(49);

        $this->assertSerialisedPacketEquals($expected, $packet->get() . 1);
    }

    public function testPublishStandardWithDup()
    {
        $packet = new Publish(new Version4());
        $packet->setDup(true);

        $expected =
            chr(0b00111000) .
            chr(2) .
            chr(0) .
            chr(0) .
            chr(49);

        $this->assertSerialisedPacketEquals($expected, $packet->get() . 1);
    }

    public function testPublishStandardWithRetain()
    {
        $packet = new Publish(new Version4());
        $packet->setRetain(true);

        $expected =
            chr(0b00110001) .
            chr(2) .
            chr(0) .
            chr(0) .
            chr(49);

        $this->assertSerialisedPacketEquals($expected, $packet->get() . 1);
    }

    public function testPublishWithPayload()
    {
        $packet = new Publish(new Version4());
        $packet->addRawToPayLoad('This is the payload');

        $expected =
            chr(0b00110000) .
            chr(21) .
            chr(0) .
            chr(0) .
            'This is the payload';

        $this->assertEquals('This is the payload', $packet->getPayload());

        $this->assertSerialisedPacketEquals($expected, $packet->get());
    }

    public function testTopic()
    {
        $packet = new Publish(new Version4());

        $packet->setTopic('topic/test');

        $expected =
            chr(0b00110000) .
            chr(12) .
            chr(0) .
            chr(10) .
            'topic/test';

        $this->assertEquals('topic/test', $packet->getTopic());

        $this->assertSerialisedPacketEquals(
            $expected,
            $packet->get()
        );
    }

    public function testSetQos()
    {
        $qos = 2;

        $packet = new Publish(new Version4());
        $packet->setQos($qos);

        $reflection = new ReflectionClass('oliverlorenz\reactphpmqtt\packet\Publish');
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

        $packet = new Publish(new Version4());
        $packet->setDup($dup);

        $reflection = new ReflectionClass('oliverlorenz\reactphpmqtt\packet\Publish');
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

        $packet = new Publish(new Version4());
        $packet->setRetain($retain);

        $reflection = new ReflectionClass('oliverlorenz\reactphpmqtt\packet\Publish');
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

        $packet = new Publish(new Version4());
        $return = $packet->setTopic($topic);
        $this->assertInstanceOf('oliverlorenz\reactphpmqtt\packet\Publish', $return);
    }

    public function testSetMessageId()
    {
        $messageId = 1;

        $packet = new Publish(new Version4());
        $packet->setMessageId($messageId);

        $reflection = new ReflectionClass('oliverlorenz\reactphpmqtt\packet\Publish');
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

        $packet = new Publish(new Version4());
        $return = $packet->setMessageId($messageId);
        $this->assertInstanceOf('oliverlorenz\reactphpmqtt\packet\Publish', $return);
    }

    public function qosProvider() {
        return array(
            array(0, 0b00110000),
            array(1, 0b00110010),
            array(2, 0b00110100),
        );
    }

    /**
     * @dataProvider qosProvider
     */
    public function testParseWithQos($qos, $byte1)
    {
        $input =
            chr($byte1) .
//            chr(4) .
            chr(2) .
            chr(0) .
            chr(0) /*.
            chr(0) .
            chr(10)*/
        ;
        $parsedPacket = Publish::parse(new Version4(), $input);

        $comparisonPacket = new Publish(new Version4());
        $comparisonPacket->setQos($qos);

        $this->assertPacketEquals($comparisonPacket, $parsedPacket);
    }

    public function testParseWithRetain()
    {
        $input =
            chr(0b00110001) .
//            chr(4) .
            chr(2) .
            chr(0) .
            chr(0) /*.
            chr(0) .
            chr(10)*/
        ;
        $parsedPacket = Publish::parse(new Version4(), $input);

        $comparisonPacket = new Publish(new Version4());
        $comparisonPacket->setRetain(true);

        $this->assertPacketEquals($comparisonPacket, $parsedPacket);
    }

    public function testParseWithDup()
    {
        $input =
            chr(0b00111000) .
//            chr(4) .
            chr(2) .
            chr(0) .
            chr(0) /*.
            chr(0) .
            chr(10)*/
        ;
        $parsedPacket = Publish::parse(new Version4(), $input);

        $comparisonPacket = new Publish(new Version4());
        $comparisonPacket->setDup(true);

        $this->assertPacketEquals($comparisonPacket, $parsedPacket);
    }

    public function testParseWithPayload()
    {
        $expectedPacket = new Publish(new Version4());
        $expectedPacket->addRawToPayLoad('My payload');

        $input =
            chr(0b00110000) .
            chr(12) .
            chr(0) .
            chr(0) .
            'My payload';

        $parsedPacket = Publish::parse(new Version4(), $input);

        $this->assertPacketEquals($expectedPacket, $parsedPacket);
    }

    private function assertPacketEquals(Publish $expected, Publish $actual)
    {
        $this->assertEquals($expected, $actual);
        $this->assertSerialisedPacketEquals($expected->get(), $actual->get());
    }

    private function assertSerialisedPacketEquals($expected, $actual)
    {
        $this->assertEquals(
            MessageHelper::getReadableByRawString($expected),
            MessageHelper::getReadableByRawString($actual)
        );
    }
}
