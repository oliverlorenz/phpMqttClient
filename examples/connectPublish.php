<?php

use oliverlorenz\reactphpmqtt\ClientFactory;
use oliverlorenz\reactphpmqtt\protocol\Version4;
use React\Stream\Stream;

require __DIR__ . '/../vendor/autoload.php';

$config = require 'config.php';

$connector = ClientFactory::createClient(new Version4(), '8.8.8.8');

$p = $connector->create($config['server'], $config['port'], $config['options']);
$p->then(function(Stream $stream) use ($connector) {
    return $connector->publish($stream, 'hello/world', 'example message');
});

$connector->getLoop()->run();
