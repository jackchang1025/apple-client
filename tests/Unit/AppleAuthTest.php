<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Apple\Client\AppleAuth;
use Apple\Client\AppleClient;
use Apple\Client\Config\Config;
use Apple\Client\Integrations\AppleAuth\AppleAuthConnector;
use Apple\Client\Integrations\AppleAuth\Request\Complete;
use Apple\Client\Integrations\AppleAuth\Request\Init;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Saloon\Http\Response;

// 创建一个测试类来使用 AppleAuth trait
class AppleAuthTest
{
    use AppleAuth;

    protected AppleAuthConnector $appleAuthConnector;

    public function __construct(AppleAuthConnector $connector)
    {
        $this->appleAuthConnector = $connector;
    }

    public function getAppleAuthConnector(): AppleAuthConnector
    {
        return $this->appleAuthConnector;
    }
}

beforeEach(function () {
    $this->config = new Config([
        'apple_auth' => [
            'url' => 'https://auth.apple.com',
        ],
    ]);

    $appleClient = new AppleClient($this->config);

    // 使用 Mockery 创建 AppleAuthConnector 的模拟对象
    $this->connector = new AppleAuthConnector($appleClient);

    // 实例化使用 AppleAuth trait 的测试类
    $this->appleAuth = new AppleAuthTest($this->connector);
});

it('successfully initializes Apple Auth', function () {
    // 设置特定的模拟响应
    MockClient::global([
        Init::class => MockResponse::make(
            body: ['key' => 'test_key', 'value' => 'test_value']
        ),
    ]);

    // 调用 appleAuthInit 方法
    $response = $this->appleAuth->appleAuthInit('test@example.com');

    // 断言响应
    expect($response)->toBeInstanceOf(Response::class)
        ->and($response->json('key'))->toBe('test_key')
        ->and($response->json('value'))->toBe('test_value');
});

it('throws exception when key is empty in appleAuthInit', function () {
    // 设置特定的模拟响应
    MockClient::global([
        Init::class => MockResponse::make(
            body: [
                'value' => 'test_value',
            ]
        ),
    ]);

    $this->appleAuth->appleAuthInit('test@example.com');
})->throws(InvalidArgumentException::class, 'key IS EMPTY');

it('throws exception when value is empty in appleAuthInit', function () {
    // 设置特定的模拟响应
    MockClient::global([
        Init::class => MockResponse::make(
            body: [
                'key' => 'test_key',
            ]
        ),
    ]);

    $this->appleAuth->appleAuthInit('test@example.com');
})->throws(InvalidArgumentException::class, 'value IS EMPTY');

it('successfully completes Apple Auth', function () {
    // 设置特定的模拟响应
    MockClient::global([
        Complete::class => MockResponse::make(
            body: [
                'M1' => 'test_m1',
                'M2' => 'test_m2',
                'c' => 'test_c',
            ]
        ),
    ]);

    $response = $this->appleAuth->appleAuthComplete('key', 'salt', 'b', 'c', 'password', '1000', 'protocol');

    expect($response)->toBeInstanceOf(Response::class)
        ->and($response->json('M1'))->toBe('test_m1')
        ->and($response->json('M2'))->toBe('test_m2')
        ->and($response->json('c'))->toBe('test_c');
});

it('throws exception when M1 is empty in appleAuthComplete', function () {
    // 设置特定的模拟响应
    MockClient::global([
        Complete::class => MockResponse::make(
            body: [
                'M2' => 'test_m2',
                'c' => 'test_c',
            ]
        ),
    ]);

    $this->appleAuth->appleAuthComplete('key', 'salt', 'b', 'c', 'password', '1000', 'protocol');
})->throws(InvalidArgumentException::class, 'M1 IS EMPTY');

it('throws exception when M2 is empty in appleAuthComplete', function () {
    // 设置特定的模拟响应
    MockClient::global([
        Complete::class => MockResponse::make(
            body: [
                'M1' => 'test_m1',
                'c' => 'test_c',
            ]
        ),
    ]);

    $this->appleAuth->appleAuthComplete('key', 'salt', 'b', 'c', 'password', '1000', 'protocol');
})->throws(InvalidArgumentException::class, 'M2 IS EMPTY');

it('throws exception when c is empty in appleAuthComplete', function () {
    // 设置特定的模拟响应
    MockClient::global([
        Complete::class => MockResponse::make(
            body: [
                'M1' => 'test_m1',
                'M2' => 'test_m2',
            ]
        ),
    ]);

    $this->appleAuth->appleAuthComplete('key', 'salt', 'b', 'c', 'password', '1000', 'protocol');
})->throws(InvalidArgumentException::class, 'c IS EMPTY');
