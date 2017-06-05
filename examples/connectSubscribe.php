<?php

require __DIR__ . '/../vendor/autoload.php';

$config = include('config.php');

$loop = React\EventLoop\Factory::create();

$dnsResolverFactory = new React\Dns\Resolver\Factory();
$resolver = $dnsResolverFactory->createCached('192.168.0.1', $loop);

$version = new oliverlorenz\reactphpmqtt\protocol\Version4();
$connector = new oliverlorenz\reactphpmqtt\Connector($loop, $resolver, $version);

$p = $connector->create($config['server'], $config['port']);
$p->then(function(\React\Stream\Stream $stream) use ($connector) {
    return $connector->subscribe($stream, '#', 0);
});

$loop->run();
