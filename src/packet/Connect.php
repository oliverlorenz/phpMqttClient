<?php
/**
 * @author Oliver Lorenz
 * @since 2015-04-24
 * Time: 00:58
 */

namespace oliverlorenz\reactphpmqtt\packet;

use oliverlorenz\reactphpmqtt\protocol\Version;

/**
 * After a Network Connection is established by a Client to a Server, the
 * first Packet sent from the Client to the Server MUST be a CONNECT Packet.
 */
class Connect extends ControlPacket {

    /** @var null|string */
    protected $clientId = null;

    /** @var null|string  */
    protected $username = null;

    /** @var null|string  */
    protected $password = null;

    /** @var bool  */
    protected $cleanSession = true;

    /** @var string|null  */
    protected $willTopic;

    /** @var string|null  */
    protected $willMessage;

    /** @var bool|null  */
    protected $willQos;

    /** @var null */
    protected $willRetain;

    /**
     * @param Version $version
     * @param string|null $username
     * @param string|null $password
     * @param string|null $clientId
     * @param bool $cleanSession
     * @param string|null $willTopic
     * @param string|null $willMessage
     * @param bool|null $willQos
     * @param null $willRetain
     */
    public function __construct(
        Version $version,
        $username = null,
        $password = null,
        $clientId = null,
        $cleanSession = true,
        $willTopic = null,
        $willMessage = null,
        $willQos = null,
        $willRetain = null
    ) {
        parent::__construct($version);
        $this->clientId = $clientId;
        $this->username = $username;
        $this->password = $password;
        $this->cleanSession = boolval($cleanSession);
        $this->willTopic = $willTopic;
        $this->willMessage = $willMessage;
        $this->willQos = boolval($willQos);
        $this->willRetain = $willRetain;
        $this->buildPayload();
    }

    protected function buildPayload()
    {
        $this->addLengthPrefixedField($this->getClientId());
        if (!is_null($this->willTopic) && !is_null($this->willMessage)) {
            $this->addLengthPrefixedField($this->willTopic);
            $this->addLengthPrefixedField($this->willMessage);
        }
        if (!empty($this->username)) {
            $this->addLengthPrefixedField($this->username);
        }
        if (!empty($this->password)) {
            $this->addLengthPrefixedField($this->password);
        }
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
        $connectByte = 0;
        if ($this->cleanSession) {
            $connectByte += 1 << 1;
        }
        if (!is_null($this->willTopic) && !is_null($this->willMessage)) {
            $connectByte += 1 << 2;
        }

        if ($this->willQos) {
            $connectByte += 1 << 3;
            // 4 TODO ?
        }

        if ($this->willRetain) {
            $connectByte += 1 << 5;
        }

        if (!empty($this->password)) {
            $connectByte += 1 << 6;
        }

        if (!empty($this->username)) {
            $connectByte += 1 << 7;
        }
        return $connectByte;
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
