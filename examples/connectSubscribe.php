<?php

use oliverlorenz\reactphpmqtt\packet\Publish;

require __DIR__ . '/../vendor/autoload.php';

$config = include('config.php');

$loop = React\EventLoop\Factory::create();

$dnsResolverFactory = new React\Dns\Resolver\Factory();
$resolver = $dnsResolverFactory->createCached('8.8.8.8', $loop);

$version = new oliverlorenz\reactphpmqtt\protocol\Version4();
$connector = new oliverlorenz\reactphpmqtt\Connector($loop, $resolver, $version);

$p = $connector->create($config['server'], $config['port'], $config['options']);
$p->then(function(\React\Stream\Stream $stream) use ($connector) {
    $stream->on(Publish::EVENT, function(Publish $message) {
        printf(
            'Received payload "%s" for topic "%s"%s',
            $message->getPayload(),
            $message->getTopic(),
            PHP_EOL
        );
    });

    return $connector->subscribe($stream, 'hello/world', 0);
});

$loop->run();
