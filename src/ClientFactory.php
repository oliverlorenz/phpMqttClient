<?php

namespace oliverlorenz\reactphpmqtt;

use oliverlorenz\reactphpmqtt\protocol\Version;
use React\Dns\Resolver\Factory as DnsResolverFactory;
use React\EventLoop\Factory as EventLoopFactory;
use React\Socket\DnsConnector;
use React\Socket\SecureConnector;
use React\Socket\TcpConnector;

class ClientFactory
{
    public static function createClient(Version $version, $resolverIp = '8.8.8.8')
    {
        $loop = EventLoopFactory::create();
        $connector = self::createDnsConnector($resolverIp, $loop);

        return new Connector($loop, $connector, $version);
    }

    public static function createSecureClient(Version $version, $resolverIp = '8.8.8.8')
    {
        $loop = EventLoopFactory::create();
        $connector = self::createDnsConnector($resolverIp, $loop);
        $secureConnector = new SecureConnector($connector, $loop);

        return new Connector($loop, $secureConnector, $version);
    }

    private static function createDnsConnector($resolverIp, $loop)
    {
        $dnsResolverFactory = new DnsResolverFactory();
        $resolver = $dnsResolverFactory->createCached($resolverIp, $loop);

        return new DnsConnector(new TcpConnector($loop), $resolver);
    }
}
