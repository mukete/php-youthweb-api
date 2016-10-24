# PHP Youthweb API

[![Latest Version](https://img.shields.io/github/release/youthweb/php-youthweb-api.svg)](https://github.com/youthweb/php-youthweb-api/releases)
[![Software License GPLv3](http://img.shields.io/badge/License-GPLv3-brightgreen.svg)](LICENSE)
[![Build Status](http://img.shields.io/travis/youthweb/php-youthweb-api.svg)](https://travis-ci.org/youthweb/php-youthweb-api)
[![Coverage Status](https://coveralls.io/repos/youthweb/php-youthweb-api/badge.svg?branch=develop&service=github)](https://coveralls.io/github/youthweb/php-youthweb-api?branch=develop)
[![Gitter](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/youthweb/youthweb-api?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

PHP Youthweb API ist ein objektorientierter Wrapper in PHP 5.6+ für die [Youthweb API](https://github.com/youthweb/youthweb-api).

Unterstütze API Version: 0.6

## Installation

[Composer](http://getcomposer.org/):

```
$ composer require youthweb/php-youthweb-api
```

## [Dokumentation](docs/README.md) / Anwendung

```php
// Create a Client
$client = new \Youthweb\Api\Client();
// Add your credentials
$client->setUserCredentials('Username', 'lp4LExKDYug5ARka6ckgkbRCzOzdd6Mo');

// Request a user
$response = $client->getResource('users')->show(123456);

echo $response->get('data.attributes.username');
// echoes 'Username'
```

Weitere Informationen zur Anwendung gibt es in der [Dokumentation](docs/README.md).

## Tests

```
phpunit
```

## [Changelog](CHANGELOG.md)

Der Changelog ist [hier](CHANGELOG.md) zu finden und folgt den Empfehlungen von [keepachangelog.com](http://keepachangelog.com/).

## Todo

- Request Error Handling
