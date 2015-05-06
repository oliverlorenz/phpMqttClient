<?php
/**
 * @author Oliver Lorenz
 * @since 2015-05-06
 * Time: 21:10
 */

namespace oliverlorenz\reactphpmqtt\packet;

use oliverlorenz\reactphpmqtt\protocol\Version;

class Subscribe extends ControlPacket {

    const COMMAND = 0x80;

    protected $topic;

    public function __construct(Version $version, $topic)
    {
        parent::__construct($version, static::COMMAND);
    }

    /**
     * @return string
     */
    protected function getVariableHeader()
    {
        return $this->getLengthPrefixField($this->topic)
        // keep alive
        . chr(0x00)
        . chr(0x0a)
            ;
    }
}