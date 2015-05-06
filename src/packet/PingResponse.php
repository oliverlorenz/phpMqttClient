<?php
/**
 * @author Oliver Lorenz
 * @since 2015-05-05
 * Time: 19:12
 */

namespace oliverlorenz\reactphpmqtt\packet;

use oliverlorenz\reactphpmqtt\protocol\Version;

class PingResponse implements Message {

    const IDENTIFIER = 0xD0;

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