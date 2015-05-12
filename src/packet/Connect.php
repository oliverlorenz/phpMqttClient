<?php
/**
 * @author Oliver Lorenz
 * @since 2015-04-24
 * Time: 00:58
 */

namespace oliverlorenz\reactphpmqtt\packet;

use oliverlorenz\reactphpmqtt\protocol\Version;

class Connect extends ControlPacket {

    /** @var bool */
    protected $useVariableHeader = true;

    /** @var null|string */
    protected $clientId = null;

    /**
     * @param Version $version
     */
    public function __construct(Version $version, $clientId = null)
    {
        parent::__construct($version);
        $this->clientId = $clientId;
        $this->addLengthPrefixedField($this->getClientId());
    }

    /**
     * @return int
     */
    public static function getControlPacketType()
    {
        return ControlPacketType::CONNECT;
    }

    /**
     * @return string
     */
    protected function getVariableHeader()
    {
        return chr(ControlPacketType::MOST_SIGNIFICANT_BYTE)         // byte 1
        . chr(strlen($this->version->getProtocolIdentifierString())) // byte 2
        . $this->version->getProtocolIdentifierString()              // byte 3,4,5,6
        . chr($this->version->getProtocolVersion())                  // byte 7
        . chr($this->getConnectFlags())                              // byte 8
        . chr(0)                                                     // byte 9
        . chr(10)                                                    // byte 10
        ;
    }

    /**
     * @return int
     */
    protected function getConnectFlags()
    {
        return 1; // TODO
    }

    /**
     * @return string
     */
    public function getClientId()
    {
        if (is_null($this->clientId)) {
            $this->clientId = md5(microtime());
        }
        return substr($this->clientId, 0, 23);
    }
}