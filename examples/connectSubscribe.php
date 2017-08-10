<?php

use oliverlorenz\reactphpmqtt\ClientFactory;
use oliverlorenz\reactphpmqtt\packet\Publish;
use oliverlorenz\reactphpmqtt\protocol\Version4;
use React\Socket\ConnectionInterface as Stream;

require __DIR__ . '/../vendor/autoload.php';

$config = require 'config.php';

$client = ClientFactory::createClient(new Version4(), '8.8.8.8');

$p = $client->connect($config['broker'], $config['options']);
$p->then(function(Stream $stream) use ($client) {
    $stream->on(Publish::EVENT, function(Publish $message) {
        printf(
            'Received payload "%s" for topic "%s"%s',
            $message->getPayload(),
            $message->getTopic(),
            PHP_EOL
        );
    });

    $client->subscribe($stream, 'hello/world', 0);
});

$client->getLoop()->run();
