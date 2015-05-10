# ReactPHP MQTT

reactMqtt is a mqtt client library PHP. Its based on the reactPHP socket-client and added the mqtt protocol specific functions. I hope its a better starting point that the existing php mqtt libraries. 

![build status](https://travis-ci.org/oliverlorenz/reactphpmqtt.svg) ![code climate](https://d3s6mut3hikguw.cloudfront.net/github/oliverlorenz/reactphpmqtt/badges/gpa.svg) ![coverage](https://d3s6mut3hikguw.cloudfront.net/github/oliverlorenz/reactphpmqtt/badges/coverage.svg)

### Notice - (May 6th, 2015)
This is the first initial commit. Only some things work already:
* Connect
* Connection Ack
* publish

I will add more features if I need them. If you need features: please give feedback or contribute to get this library running.

## Goal

Goal of this project is easy to use mqtt client for PHP in a modern architecture. Currently, only protocol version 4 (mqtt 3.1.1) is implemented.
* Protocol specifications: http://docs.oasis-open.org/mqtt/mqtt/v3.1.1/csprd02/mqtt-v3.1.1-csprd02.html

## Example publish
```php
<?php

require __DIR__ . '/../vendor/autoload.php';

$loop = React\EventLoop\Factory::create();

$dnsResolverFactory = new React\Dns\Resolver\Factory();
$resolver = $dnsResolverFactory->createCached('8.8.8.8', $loop);

$version = new oliverlorenz\reactphpmqtt\protocol\Version4();
$connector = new oliverlorenz\reactphpmqtt\Connector($loop, $resolver, $version);

$connector->create('yourmqttserver.tdl', 1883);
$connector->onConnected(function() use ($connector) {
    $connector->publish('a/b', 'example message');
});
$loop->run();

```

#Troubleshooting
## Why does the connect to localhost:1883 not work?
The answer is simple: In the example is the DNS 8.8.8.8 configured. Your local server is not visible for them, so you can't connect.
