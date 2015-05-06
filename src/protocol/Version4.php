<?php
/**
 * @author Oliver Lorenz
 * @since 2015-04-22
 * Time: 18:12
 */

namespace oliverlorenz\reactphpmqtt\protocol;

class Version4 implements Version {

    function getProtocolIdentifierString()
    {
        return 'MQTT';
    }

    /** @return int */
    function getProtocolVersion()
    {
        return 4;
    }
}