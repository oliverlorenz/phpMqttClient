<?php

require __DIR__ . '/../vendor/autoload.php';

$config = include('config.php');

$loop = React\EventLoop\Factory::create();

$dnsResolverFactory = new React\Dns\Resolver\Factory();
$resolver = $dnsResolverFactory->createCached('8.8.8.8', $loop);

$version = new oliverlorenz\reactphpmqtt\protocol\Version4();
$connector = new oliverlorenz\reactphpmqtt\Connector($loop, $resolver, $version);

$connector->create($config['server'], 1883);
$connector->onConnected(function() use ($connector) {
    $i = 0;
    // for($i = 0; $i < 300; $i++) {
    $connector->publish('a/b', 'example message');

});
$loop->run();

