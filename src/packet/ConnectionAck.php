<?php
/**
 * @author Oliver Lorenz
 * @since 2015-04-24
 * Time: 19:40
 */

namespace oliverlorenz\reactphpmqtt\packet;

use oliverlorenz\reactphpmqtt\protocol\Version;

class ConnectionAck implements Message {

    const IDENTIFIER = 0x20;

    public function __construct(Version $version, $input)
    {
        $this->version = $version;
        $this->input = $input;
    }

    public function getIdentifier()
    {
        return static::IDENTIFIER;
    }
}