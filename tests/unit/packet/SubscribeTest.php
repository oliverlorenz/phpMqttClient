<?php
/**
 * @author Oliver Lorenz
 * @since 2015-05-10
 * Time: 11:29
 */

use \oliverlorenz\reactphpmqtt\packet\MessageHelper;

class SubscribeTest extends PHPUnit_Framework_TestCase {

    public function testGetControlPacketType()
    {
        $version = new \oliverlorenz\reactphpmqtt\protocol\Version4();
        $packet = new \oliverlorenz\reactphpmqtt\packet\Subscribe($version);
        $this->assertEquals(
            $packet->getControlPacketType(),
            8
        );
    }

    public function testGetHeaderTestFixedHeader()
    {
        $version = new \oliverlorenz\reactphpmqtt\protocol\Version4();
        $packet = new \oliverlorenz\reactphpmqtt\packet\Subscribe($version);

        $subcriptionTopic = 'a/b';
        $packet->addSubscription($subcriptionTopic, 0);

        $this->assertEquals(
            MessageHelper::getReadableByRawString(substr($packet->get(), 0, 2)),
            MessageHelper::getReadableByRawString(chr(130) . chr(8))
        );
    }

    public function testGetHeaderTestFixedHeaderWithTwoSubscribedTopics()
    {
        $version = new \oliverlorenz\reactphpmqtt\protocol\Version4();
        $packet = new \oliverlorenz\reactphpmqtt\packet\Subscribe($version);

        $subscriptionTopic = 'a/b';
        $packet->addSubscription($subscriptionTopic, 1);

        $subscriptionTopic = 'c/d';
        $packet->addSubscription($subscriptionTopic, 2);

        $this->assertEquals(
            MessageHelper::getReadableByRawString(substr($packet->get(), 0, 2)),
            MessageHelper::getReadableByRawString(chr(130) . chr(14))
        );
    }


}