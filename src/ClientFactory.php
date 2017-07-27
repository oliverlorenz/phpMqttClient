<?php

namespace oliverlorenz\reactphpmqtt;

use oliverlorenz\reactphpmqtt\protocol\Version;
use React\Dns\Resolver\Factory as DnsResolverFactory;
use React\EventLoop\Factory as EventLoopFactory;

class ClientFactory
{
    public static function createClient(Version $version, $resolverIp)
    {
        $loop = EventLoopFactory::create();
        $resolver = self::createDnsResolver($resolverIp, $loop);

        return new Connector($loop, $resolver, $version);
    }

    public static function createSecureClient(Version $version, $resolverIp)
    {
        $loop = EventLoopFactory::create();
        $resolver = self::createDnsResolver($resolverIp, $loop);

        return new SecureConnector($loop, $resolver, $version);
    }

    private static function createDnsResolver($resolverIp, $loop)
    {
        $dnsResolverFactory = new DnsResolverFactory();

        return $dnsResolverFactory->createCached($resolverIp, $loop);
    }
}
