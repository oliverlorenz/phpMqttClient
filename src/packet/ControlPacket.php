<?php
/**
 * @author Oliver Lorenz
 * @since 2015-04-24
 * Time: 00:32
 */

namespace oliverlorenz\reactphpmqtt\packet;

use oliverlorenz\reactphpmqtt\protocol\Version;

abstract class ControlPacket {

    protected $command;

    /** @var $version Version */
    protected $version;


    protected $variableHeader;

    protected $payload = '';

    protected $useVariableHeader = false;
    protected $useFixedHeader = true;

    protected $identifier;

    public function __construct(Version $version)
    {
        $this->version = $version;
    }

    /**
     * @param Version $version
     * @param string $rawInput
     * @return static
     */
    public static function parse(Version $version, $rawInput)
    {
        $packet = new static($version);
        static::checkRawInputValidControlPackageType($rawInput);
        return $packet;
    }

    protected static function checkRawInputValidControlPackageType($rawInput)
    {
        $packetType = ord($rawInput{0}) >> 4;
        if ($packetType !== static::getControlPacketType()) {
            throw new \RuntimeException('raw input is not valid for this control packet');
        }
    }

    /** @return null */
    public static function getControlPacketType() {
        throw new \RuntimeException('you should overwrite getControlPacketType()');
    }

    protected function getPayloadLength()
    {
        return strlen($this->getPayload());
    }

    public function getPayload()
    {
        return $this->payload;
    }

    protected function getRemainingLength()
    {
        return strlen($this->getVariableHeader()) + $this->getPayloadLength();
    }

    /**
     * @return string
     */
    protected function getFixedHeader()
    {
        // Figure 3.8
        $byte1 = static::getControlPacketType() << 4;
        $byte1 = $this->addReservedBitsToFixedHeaderControlPacketType($byte1);

        $byte2 = $this->getRemainingLength();

        return chr($byte1)
             . chr($byte2);
    }

    /**
     * @return string
     */
    protected function getVariableHeader()
    {
        return null;
    }

    /**
     * @param $stringToAdd
     */
    public function addRawToPayLoad($stringToAdd)
    {
        $this->payload .= $stringToAdd;
    }

    /**
     * @param $fieldPayload
     */
    public function addLengthPrefixedField($fieldPayload)
    {
        $return = $this->getLengthPrefixField($fieldPayload);
        $this->addRawToPayLoad($return);
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

    public function get()
    {
        $fullMessage = '';

        // add fixed header
        if ($this->useFixedHeader) {
            $fullMessage .= $this->getFixedHeader();
        }

        // add variable header
        if ($this->useVariableHeader) {
            $fullMessage .= $this->getVariableHeader();
        }

        // add payload
        $fullMessage .= $this->getPayload();

        return $fullMessage;
    }

    /**
     * @param $byte1
     * @return $byte1 unmodified
     */
    protected function addReservedBitsToFixedHeaderControlPacketType($byte1)
    {
        return $byte1;
    }

    /**
     * @param int $startIndex
     * @param string $rawInput
     * @return string
     */
    protected static function getPayloadLengthPrefixFieldInRawInput($startIndex, $rawInput)
    {
        $headerLength = 2;
        $header = substr($rawInput, $startIndex, $headerLength);
        $lengthOfMessage = ord($header{1});
        return substr($rawInput, $startIndex + $headerLength, $lengthOfMessage);
    }


}
