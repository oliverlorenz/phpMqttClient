<?php
/**
 * @author Oliver Lorenz
 * @since 2015-05-08
 * Time: 16:54
 */

namespace oliverlorenz\reactphpmqtt\packet;


class ControlPacketType {

    const CONNECT = 1;
    const CONNACK = 2;
    const PUBLISH = 3;
    const PUBACK = 4;
    const PUBREC = 5;
    const PUBREL = 6;
    const PUBCOMP = 7;
    const SUBSCRIBE = 8;
    const SUBACK = 9;
    const UNSUBSCRIBE = 10;
    const UNSUBACK = 11;
    const PINGREQ = 12;
    const PINGRESP = 13;
    const DISCONNECT = 14;

    const MOST_SIGNIFICANT_BYTE = 0;
}