<?php
/**
 * @author Oliver Lorenz
 * @since 2015-04-24
 * Time: 00:58
 */

namespace oliverlorenz\reactphpmqtt\packet;

use oliverlorenz\reactphpmqtt\protocol\Version;

class Connect extends ControlPacket {

    protected $useVariableHeader = true;

    const COMMAND = 0x10;

    public function __construct(Version $version)
    {
        parent::__construct($version, static::COMMAND);
    }

    /**
     * @return string
     */
    protected function getVariableHeader()
    {
        return chr(0x00)
        . chr(strlen($this->version->getProtocolIdentifierString()))
        . $this->version->getProtocolIdentifierString()
        // protocol level
        . chr($this->version->getProtocolVersion())
        // connect flag
        . chr(0x00)
        // keep alive
        . chr(0x00)
        . chr(0x0a)
            ;
    }
}