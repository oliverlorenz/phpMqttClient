<?php

namespace oliverlorenz\reactphpmqtt\packet\QoS;

/**
 * Quality of Service levels
 *
 * @see http://docs.oasis-open.org/mqtt/mqtt/v3.1.1/os/mqtt-v3.1.1-os.html#_Toc398718099
 *
 * @author Alin Eugen Deac <ade@vestergaardcompany.com>
 * @package oliverlorenz\reactphpmqtt\packet\QoS
 */
interface Levels
{
    /**
     * QoS 0
     *
     * The message is delivered according to the capabilities of the underlying network.
     * No response is sent by the receiver and no retry is performed by the sender.
     * The message arrives at the receiver either once or not at all.
     */
    const AT_MOST_ONCE_DELIVERY = 0;

    /**
     * QoS 1
     *
     * This quality of service ensures that the message arrives at the receiver at least once.
     */
    const AT_LEAST_ONCE_DELIVERY = 1;

    /**
     * QoS 2
     *
     * This is the highest quality of service, for use when neither loss nor duplication of
     * messages are acceptable. There is an increased overhead associated with this
     * quality of service.
     */
    const EXACTLY_ONCE_DELIVERY = 2;
}