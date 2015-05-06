<?php
/**
 * @author Oliver Lorenz
 * @since 2015-04-24
 * Time: 00:32
 */

namespace oliverlorenz\reactphpmqtt\packet;

use oliverlorenz\reactphpmqtt\protocol\Version;
use oliverlorenz\reactphpmqtt\protocol\Version4;

class ControlPacket implements Message {

    protected $command;

    /** @var $version Version4 */
    protected $version;

    protected $fixedHeader;

    protected $variableHeader;

    protected $payload;

    protected $useVariableHeader = false;

    protected $identifier;

    public function __construct(Version $version, $command)
    {
        $this->version = $version;
        $this->command = $command;
    }

    public function getIdentifier()
    {
        return $this->identifier;
    }

    protected function getPayloadLength()
    {
        return strlen($this->payload);
    }

    protected function getFixedHeader()
    {
        $contentLength = $this->getPayloadLength();
        if (!is_null($this->getVariableHeader())) {
            $contentLength += strlen($this->getVariableHeader());
        }

        return chr($this->command)
             . chr($contentLength)
            ;
    }

    /**
     * @return string
     */
    protected function getVariableHeader()
    {
        return null;
    }

    /**
     * @return string
     */
    public function get()
    {
        $return = $this->getFixedHeader();
        if (!is_null($this->getVariableHeader())) {
            $return .= $this->getVariableHeader();
        }
        $return .= $this->payload;

        return $return;
    }

    /**
     * @param $stringToAdd
     */
    public function addToPayLoad($stringToAdd)
    {
        $this->payload .= $stringToAdd;
    }

    /**
     * @param $fieldPayload
     * @return string
     */
    public function addLengthPrefixedField($fieldPayload)
    {
        $return = $this->getLengthPrefixField($fieldPayload);
        $this->addToPayLoad($return);
    }

    public function getLengthPrefixField($fieldPayload)
    {
        $stringLength = strlen($fieldPayload);
        $msb = $stringLength >> 8;
        $lsb = $stringLength % 256;
        $return = chr($msb);
        $return .= chr($lsb);
        $return .= $fieldPayload;
        return $return;
    }

}