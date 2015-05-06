<?php
/**
 * @author Oliver Lorenz
 * @since 2015-05-05
 * Time: 19:03
 */

namespace oliverlorenz\reactphpmqtt\packet;

use oliverlorenz\reactphpmqtt\protocol\Version;

class PingRequest extends ControlPacket {

    protected $useVariableHeader = true;

    const COMMAND = 0xc0;

    public function __construct(Version $version)
    {
        parent::__construct($version, static::COMMAND);
    }
}