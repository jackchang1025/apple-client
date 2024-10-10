<?php

declare(strict_types=1);

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Psr\Log\LoggerInterface;
use Saloon\Exceptions\Request\ClientException;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Weijiajia\AppleClient;
use Weijiajia\Config\Config;
use Weijiajia\Cookies\Cookies;
use Weijiajia\Integrations\AppleAuth\AppleAuthConnector;
use Weijiajia\Integrations\AppleId\AppleIdConnector;
use Weijiajia\Integrations\Idmsa\IdmsaConnector;
use Weijiajia\Response\Response;
use Weijiajia\Store\CacheStore;

beforeEach(function () {
    $this->config = new Config([
        'apple_auth' => [
            'url' => 'https://auth.apple.com',
        ],
    ]);
});

it('throws exception when initializing AppleClient without apple_auth config', function () {
    expect(fn () => new AppleClient(new Config()))
        ->toThrow(InvalidArgumentException::class, 'apple_auth config is empty');
});

it('init AppleClient with config', function () {
    $client = new AppleClient($this->config);

    expect($client)->toBeInstanceOf(AppleClient::class)
        ->and($client->getAppleIdConnector())->toBeInstanceOf(AppleIdConnector::class)
        ->and($client->getIdmsaConnector())->toBeInstanceOf(IdmsaConnector::class)
        ->and($client->getAppleAuthConnector())->toBeInstanceOf(AppleAuthConnector::class)
        ->and($client->getLogger())->toEqual(null)
        ->and($client->getProxy())->toEqual(null)
        ->and($client->getCookieJar())->toEqual(null)
        ->and($client->getHeaderRepositories())->toEqual(null)
        ->and($client->config())->toBeInstanceOf(Config::class);
});

it('init AppleClient', function () {
    $cookieStore = Mockery::mock(Cookies::class);
    $headerStore = Mockery::mock(CacheStore::class);
    $logger = Mockery::mock(LoggerInterface::class);

    $client = new AppleClient(
        config: $this->config,
        headerRepositories: $headerStore,
        cookieJar: $cookieStore,
        logger:$logger
    );

    $proxy = 'http://127.0.0.1:8080';
    $client->withProxy($proxy);

    expect($client)->toBeInstanceOf(AppleClient::class)
        ->and($client->getAppleIdConnector())->toBeInstanceOf(AppleIdConnector::class)
        ->and($client->getIdmsaConnector())->toBeInstanceOf(IdmsaConnector::class)
        ->and($client->getAppleAuthConnector())->toBeInstanceOf(AppleAuthConnector::class)
        ->and($client->getLogger())->toEqual($logger)
        ->and($client->config())->toEqual($this->config)
        ->and($client->getProxy())->toEqual($proxy)
        ->and($client->getCookieJar())->toEqual($cookieStore)
        ->and($client->getHeaderRepositories())->toEqual($headerStore)
        ->and($client->config())->toBeInstanceOf(Config::class)

        ->and($client->getAppleIdConnector()->getProxy())->toEqual($proxy)
        ->and($client->getAppleIdConnector()->getCookieJar())->toEqual($cookieStore)
        ->and($client->getAppleIdConnector()->getHeaderRepositories())->toEqual($headerStore)
        ->and($client->getAppleIdConnector()->config())->toEqual($this->config)
        ->and($client->getAppleIdConnector()->getLogger())->toEqual($logger)

        ->and($client->getIdmsaConnector()->getProxy())->toEqual($proxy)
        ->and($client->getIdmsaConnector()->getCookieJar())->toEqual($cookieStore)
        ->and($client->getIdmsaConnector()->getHeaderRepositories())->toEqual($headerStore)
        ->and($client->getIdmsaConnector()->config())->toEqual($this->config)
        ->and($client->getIdmsaConnector()->getLogger())->toEqual($logger);
});

it('可以执行 authLogin 流程', function () {
    // 模拟 appleAuthInit 响应
    MockClient::global()->addResponses([
        'https://auth.apple.com/init' => MockResponse::make([
            'key' => 'test_key',
            'value' => 'test_value',
        ], 200),
        'https://idmsa.apple.com/appleauth/auth/signin/init' => MockResponse::make([
            'salt' => 'test_salt',
            'b' => 'test_b',
            'c' => 'test_c',
            'iteration' => 1000,
            'protocol' => 'test_protocol',
        ], 200),
        'https://auth.apple.com/complete' => MockResponse::make([
            'M1' => 'test_m1',
            'M2' => 'test_m2',
            'c' => 'test_c',
        ], 200),
        'https://idmsa.apple.com/appleauth/auth/signin/complete?isRememberMeEnabled=true' => MockResponse::make([
            'success' => true,
        ], 409),
    ]);

    $client = new AppleClient($this->config);

    $response = $client->authLogin('test@example.com', 'password123');

    expect($response)->toBeInstanceOf(Response::class)
        ->and($response->json('success'))->toBeTrue();
});

it('在 appleAuthInit 失败时抛出异常', function () {
    MockClient::global()->addResponses([
        'https://auth.apple.com/init' => MockResponse::make([
            'error' => 'Invalid request',
        ], 400),
    ]);

    $client = new AppleClient($this->config);

    expect(fn () => $client->authLogin('test@example.com', 'password123'))
        ->toThrow(ClientException::class, 'Bad Request (400) Response: {"error":"Invalid request"}');
});
