# Apple Client

[![CN doc](https://img.shields.io/badge/文档-中文版-blue.svg)](README_CN.md)

## Introduction

Apple Client is a PHP library that integrates the [Saloon HTTP](https://docs.saloon.dev/) client to simulate Apple browser client interactions with various Apple services. It provides a simple and flexible interface for developers to easily integrate Apple's authentication, account management, and other related functionalities into their applications.

**Note:** Before using the Apple Client library, you may need to reverse engineer Apple's login process and find the algorithm for encrypting account and password.

### Key Features:

- Apple ID Authentication
- Account Management
- Phone Number Verification
- Security Code Validation
- Flexible Configuration Management
- Robust Error Handling

## System Requirements

- PHP 8.2 or higher
- Composer
- ext-simplexml
- ext-dom
- ext-libxml

## Installation

Install Apple Client using Composer:

```bash
composer require weijiajia/apple-client
```

## Usage Examples

1. Create an AppleClient instance:

```php

use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;
use Weijiajia\AppleClient;
use Weijiajia\AppleClientFactory;
use Weijiajia\Cookies\Cookies;
use Weijiajia\Store\CacheStore;


$config =  Config::fromArray([
    'apple_auth' => [
    'url' => 'https://your_apple_auth_url',
 ]);
 
// Configuration
$cookie = new Cookies(cache: new CacheInterface());

// For managing multiple accounts
$cookie = new Cookies(cache: new CacheInterface(),'your_account_name');

// Logging
$logger = new LoggerInterface();

// Synchronize and persist headers
 $headerRepositories = new CacheStore(
            cache: new CacheInterface(),
            key: 'your_account_name',
            defaultData: [
            'scnt' => 'value',
            ]
        );

// Create AppleClient instance
$client = new AppleClient(config: $config, headerRepositories: $headerRepositories,cookieJar: $cookie,logger: $logger);

// Or use AppleFactory
$factory = new AppleClientFactory(cache: $cache, logger: $logger);
$client = $factory->create('your_client_id', [
    'apple_auth' => [
    'url' => 'https://your_apple_auth_url',
    ],
]);

// Custom configuration
$client->withConfig([
'timeOutInterval' => 30,
]);
// Use proxy
$client->setProxy('http://proxy.example.com:8080');

```


2. Perform Apple ID Authentication:

```php

// login
$response = $client->authLogin('user@example.com', 'your_password');

// Two-factor authentication
$response = $client->verifySecurityCode('your_security_code');

// get token:
$response = $client->token();

```

## Important Notes

This project is for learning and research purposes only. Do not use it for illegal purposes. Usage may violate Apple's terms of service, please use with caution. Ensure that your network environment can access Apple websites normally.

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

## Contact

If you have any questions or suggestions, please contact:
- shadowmatthew1025@gmail.com