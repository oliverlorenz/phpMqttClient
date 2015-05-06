<?php
/**
 * @author Oliver Lorenz
 * @since 2015-04-24
 * Time: 01:22
 */

namespace oliverlorenz\reactphpmqtt\packet;

use oliverlorenz\reactphpmqtt\protocol\Version;

class Publish extends ControlPacket {

    const COMMAND = 0x30;

    protected $messageId;

    protected $topic;

    public function __construct(Version $version, $messageId)
    {
        parent::__construct($version, static::COMMAND);
        $this->messageId = $messageId;
    }

    public function setTopic($topic)
    {
        $this->topic = $topic;
        return $this;
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