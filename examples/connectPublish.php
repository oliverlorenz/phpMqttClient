<?php

use oliverlorenz\reactphpmqtt\ClientFactory;
use oliverlorenz\reactphpmqtt\protocol\Version4;
use React\Socket\ConnectionInterface as Connection;

require __DIR__ . '/../vendor/autoload.php';

$config = require 'config.php';

$client = ClientFactory::createClient(new Version4(), '8.8.8.8');

$p = $client->connect($config['broker'], $config['options']);
$client->getLoop()->addPeriodicTimer(10, function () use ($p, $client) {
    $p->then(function(Connection $stream) use ($client) {
        return $client->publish($stream, 'hello/world', 'example message');
    });
});

$client->getLoop()->run();
