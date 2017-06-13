<?php

require __DIR__ . '/../vendor/autoload.php';

$config = include('config.php');

$loop = React\EventLoop\Factory::create();

$dnsResolverFactory = new React\Dns\Resolver\Factory();
$resolver = $dnsResolverFactory->createCached('8.8.8.8', $loop);

$version = new oliverlorenz\reactphpmqtt\protocol\Version4();
$connector = new oliverlorenz\reactphpmqtt\Connector($loop, $resolver, $version);

$p = $connector->create($config['server'], $config['port'], $config['options']);
$p->then(function(\React\Stream\Stream $stream) use ($connector) {
    return $connector->publish($stream, 'hello/world', 'example message');
});

$loop->run();
