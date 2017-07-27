<?php

use oliverlorenz\reactphpmqtt\ClientFactory;
use oliverlorenz\reactphpmqtt\packet\Publish;
use oliverlorenz\reactphpmqtt\protocol\Version4;
use React\Stream\Stream;

require __DIR__ . '/../vendor/autoload.php';

$config = require 'config.php';

$connector = ClientFactory::createClient(new Version4(), '8.8.8.8');

$p = $connector->create($config['server'], $config['port'], $config['options']);
$p->then(function(Stream $stream) use ($connector) {
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

$connector->getLoop()->run();
