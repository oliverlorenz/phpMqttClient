# PHP MQTT Client

phpMqttClient is a mqtt client library for PHP. Its based on the reactPHP socket-client and added the mqtt protocol specific functions. I hope its a better starting point that the existing php mqtt libraries. 

[![Build Status](https://travis-ci.org/oliverlorenz/phpMqttClient.svg?branch=master)](https://travis-ci.org/oliverlorenz/phpMqttClient) 
[![Code Climate](https://codeclimate.com/github/oliverlorenz/phpMqttClient/badges/gpa.svg)](https://codeclimate.com/github/oliverlorenz/phpMqttClient) 
[![Test Coverage](https://codeclimate.com/github/oliverlorenz/phpMqttClient/badges/coverage.svg)](https://codeclimate.com/github/oliverlorenz/phpMqttClient/coverage)

### Notice - (May 12th, 2015)
This is library is not stable currently. Its an early state, but I am working on it. I will add more features if I need them. If you need features: please give feedback or contribute to get this library running.

Currently works:
* connect (clean session, no other connect flags)
* disconnect
* publish
* subscribe

## Goal

Goal of this project is easy to use MQTT client for PHP in a modern architecture without using any php modules. Currently, only protocol version 4 (mqtt 3.1.1) is implemented.
* Protocol specifications: http://docs.oasis-open.org/mqtt/mqtt/v3.1.1/csprd02/mqtt-v3.1.1-csprd02.html

## Example publish

```php
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
    return $connector->publish($stream, 'a/b', 'example message');
});
$loop->run();
```

## Example subscribe

```php
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
        print_r($message);
    });
    
    $connector->subscribe($stream, 'a/b', 0);
    $connector->subscribe($stream, 'a/c', 0);
});

$loop->run();
```

## Run tests

    ./vendor/bin/phpunit -c ./tests/phpunit.xml ./tests


## Troubleshooting

### Why does the connect to localhost:1883 not work?

The answer is simple: In the example is the DNS 8.8.8.8 configured. Your local server is not visible for them, so you can't connect.
