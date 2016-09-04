<?php
namespace oliverlorenz\reactphpmqtt;

use oliverlorenz\reactphpmqtt\protocol\Version;
use React\Dns\Resolver\Resolver;
use React\EventLoop\LoopInterface;
use React\SocketClient\SecureConnector as ReactSecureConnector;

/**
 * Secure Connector
 *
 * @author Alin Eugen Deac <ade@vestergaardcompany.com>
 * @package oliverlorenz\reactphpmqtt
 */
class SecureConnector extends Connector
{

    public function __construct(LoopInterface $loop, Resolver $resolver, Version $version)
    {
        parent::__construct($loop, $resolver, $version);

        // Overwrite the socket connection, use secure connection instead
        $this->socketConnector = new ReactSecureConnector($this->socketConnector, $loop);
    }

}