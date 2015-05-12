# ReactPHP MQTT

reactMqtt is a mqtt client library PHP. Its based on the reactPHP socket-client and added the mqtt protocol specific functions. I hope its a better starting point that the existing php mqtt libraries. 

[![Build Status](https://travis-ci.org/oliverlorenz/reactphpmqtt.svg?branch=master)](https://travis-ci.org/oliverlorenz/reactphpmqtt) [![Code Climate](https://codeclimate.com/github/oliverlorenz/reactphpmqtt/badges/gpa.svg)](https://codeclimate.com/github/oliverlorenz/reactphpmqtt) [![Test Coverage](https://codeclimate.com/github/oliverlorenz/reactphpmqtt/badges/coverage.svg)](https://codeclimate.com/github/oliverlorenz/reactphpmqtt/coverage)

### Notice - (May 12th, 2015)
This is library is not stable currently. Its an early state, but I am working on it. I will add more features if I need them. If you need features: please give feedback or contribute to get this library running.

Currently works:
* connect (clean session, no other connect flags)
* disconnect
* publish
* subscribe

## Goal

Goal of this project is easy to use mqtt client for PHP in a modern architecture without using any php modules. Currently, only protocol version 4 (mqtt 3.1.1) is implemented.
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

$connector->create($config['server'], 1883);
$connector->onConnected(function() use ($connector) {
    $connector->publish('a/b', 'example message');
});
$loop->run();
```
## Example subscribe
```
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
    $connector->subscribe('a/b', 0);
    $connector->subscribe('a/c', 0);
});
$connector->onPublishReceived(function($message) {
    print_r($message);
});
$loop->run();
```


#Troubleshooting
## Why does the connect to localhost:1883 not work?
The answer is simple: In the example is the DNS 8.8.8.8 configured. Your local server is not visible for them, so you can't connect.
