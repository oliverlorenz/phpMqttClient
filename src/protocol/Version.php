<?php
/**
 * Created by PhpStorm.
 * User: olorenz
 * Date: 20.04.15
 * Time: 23:15
 */

namespace oliverlorenz\reactphpmqtt\protocol;

interface Version {

    /** @return string */
    function getProtocolIdentifierString();

    /** @return int */
    function getProtocolVersion();
}