<?php
/**
 * @author Oliver Lorenz
 * @since 2015-05-10
 * Time: 11:29
 */

namespace oliverlorenz\reactphpmqtt\packet;

use PHPUnit_Framework_TestCase;

class SubscribeTest extends PHPUnit_Framework_TestCase {

    public function testSubscribeControlPacketTypeIsEight()
    {
        $this->assertEquals(8, Subscribe::getControlPacketType());
    }

    public function testGetHeaderTestFixedHeader()
    {
        $version = new \oliverlorenz\reactphpmqtt\protocol\Version4();
        $packet = new Subscribe($version);

        $subscriptionTopic = 'a/b';
        $packet->addSubscription($subscriptionTopic, 0);

        $this->assertEquals(
            MessageHelper::getReadableByRawString(substr($packet->get(), 0, 2)),
            MessageHelper::getReadableByRawString(chr(130) . chr(8))
        );
    }

    public function testGetHeaderTestFixedHeaderWithTwoSubscribedTopics()
    {
        $version = new \oliverlorenz\reactphpmqtt\protocol\Version4();
        $packet = new Subscribe($version);

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
