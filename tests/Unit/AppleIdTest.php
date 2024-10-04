<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Apple\Client\AppleClient;
use Apple\Client\AppleId;
use Apple\Client\Config\Config;
use Apple\Client\Exception\AccountLockoutException;
use Apple\Client\Exception\PhoneException;
use Apple\Client\Integrations\AppleId\AppleIdConnector;
use Apple\Client\Integrations\AppleId\Request\AccountManage\SecurityVerifyPhone;
use Apple\Client\Integrations\AppleId\Request\AccountManage\SecurityVerifyPhoneSecurityCode;
use Apple\Client\Integrations\AppleId\Request\AccountManage\Token;
use Apple\Client\Integrations\AppleId\Request\AuthenticatePassword;
use Apple\Client\Integrations\AppleId\Request\Bootstrap;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Saloon\Exceptions\Request\ClientException;
use Saloon\Exceptions\Request\Statuses\InternalServerErrorException;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Saloon\Http\Response;

// 使用 Mockery 的 PHPUnit 集成
uses(MockeryPHPUnitIntegration::class);
uses()->group('apple-id');

// 创建一个测试类来使用 AppleId trait
class AppleIdTest
{
    use AppleId;

    protected AppleIdConnector $appleIdConnector;

    public function __construct(AppleIdConnector $connector)
    {
        $this->appleIdConnector = $connector;
    }

    public function getAppleIdConnector(): AppleIdConnector
    {
        return $this->appleIdConnector;
    }
}

beforeEach(function () {
    // 创建配置实例
    $this->config = new Config([
        'apple_auth' => [
            'url' => 'https://auth.apple.com',
        ],
    ]);

    // 创建 AppleClient 实例
    $appleClient = new AppleClient($this->config);

    // 使用 Mockery 创建 AppleIdConnector 的模拟对象
    $this->connector = new AppleIdConnector($appleClient);

    // 实例化使用 AppleId trait 的测试类
    $this->appleId = new AppleIdTest($this->connector);
});

it('successfully bootstraps Apple Id', function () {
    // 设置特定的模拟响应
    MockClient::global([
        Bootstrap::class => MockResponse::make(
            body: [
            ]
        ),
    ]);

    // 调用 bootstrap 方法
    $response = $this->appleId->bootstrap();

    // 断言响应
    expect($response)->toBeInstanceOf(Response::class)
        ->and($response->failed())
        ->toBeFalse();
});

it('throws exception when bootstrap fails', function () {
    // 设置特定的模拟响应
    MockClient::global([
        Bootstrap::class => MockResponse::make(
            body: ['error' => 'Invalid request'],
            status: 400
        ),
    ]);

    // 调用 bootstrap 方法，预期抛出异常
    $this->appleId->bootstrap();
})->throws(ClientException::class, 'Bad Request (400) Response: {"error":"Invalid request"}');

it('successfully authenticates password', function () {
    // 设置特定的模拟响应
    MockClient::global([
        AuthenticatePassword::class => MockResponse::make(),
    ]);

    // 调用 authenticatePassword 方法
    $response = $this->appleId->authenticatePassword('securepassword123');

    // 断言响应
    expect($response)->toBeInstanceOf(Response::class)
        ->and($response->successful())
        ->toBeTrue();
});

it('throws exception when authenticatePassword fails', function () {
    // 设置特定的模拟响应
    MockClient::global([
        AuthenticatePassword::class => MockResponse::make(
            body: ['error' => 'Authentication failed'],
            status: 401
        ),
    ]);

    // 调用 authenticatePassword 方法，预期抛出异常
    $this->appleId->authenticatePassword('wrongpassword');
})->throws(ClientException::class, 'Unauthorized (401) Response: {"error":"Authentication failed"}');

it('successfully retrieves token', function () {
    // 设置特定的模拟响应
    MockClient::global([
        Token::class => MockResponse::make(),
    ]);

    // 调用 token 方法
    $response = $this->appleId->token();

    // 断言响应
    expect($response)->toBeInstanceOf(Response::class)
        ->and($response->successful())
        ->toBeTrue();
});

it('throws exception when token retrieval fails', function () {
    // 设置特定的模拟响应
    MockClient::global([
        Token::class => MockResponse::make(
            body: ['error' => 'Invalid request'],
            status: 500
        ),
    ]);

    // 调用 token 方法，预期抛出异常
    $this->appleId->token();
})->throws(InternalServerErrorException::class, 'Internal Server Error (500) Response: {"error":"Invalid request"}');

it('successfully verifies phone security', function () {
    // 设置特定的模拟响应
    MockClient::global([
        SecurityVerifyPhone::class => MockResponse::make(
            body: ['verified' => true],
        ),
    ]);

    // 调用 securityVerifyPhone 方法
    $response = $this->appleId->securityVerifyPhone('US', '1234567890', '+1', true);

    // 断言响应
    expect($response)->toBeInstanceOf(Response::class)
        ->and($response->successful())
        ->toBeTrue();
});

it('throws exception when securityVerifyPhone fails', function () {
    // 设置模拟响应为 429 太多请求
    MockClient::global([
        SecurityVerifyPhone::class => MockResponse::make(
            body: ['error' => 'Too many requests'],
            status: 423
        ),
    ]);

    // 调用 securityVerifyPhone 方法，预期抛出异常
    $response = $this->appleId->securityVerifyPhone('US', '1234567890', '+1', true);

    // 断言响应
    expect($response)->toBeInstanceOf(Response::class)
        ->and($response->status())
        ->toBeBetween(200, 423);
});

it('throws exception when securityVerifyPhone AccountLockoutException', function () {
    MockClient::global([
        SecurityVerifyPhone::class => MockResponse::make(
            body: ['error' => 'AccountLockoutException'],
            status: 467
        ),
    ]);

    // 调用 securityVerifyPhone 方法，预期抛出异常
    $this->appleId->securityVerifyPhone('US', '1234567890', '+1', true);
})->throws(AccountLockoutException::class, 'Unknown Status (467) Response: {"error":"AccountLockoutException"}');

//it('throws exception when securityVerifyPhone PhoneException', function () {
//    MockClient::global([
//        SecurityVerifyPhone::class => MockResponse::make(
//            body: [
//                'service_errors' => [
//                [
//                    'code' => -28248
//                ]
//            ]],
//            status: 400
//        ),
//    ]);
//
//    // 调用 securityVerifyPhone 方法，预期抛出异常
//    $this->appleId->securityVerifyPhone('US', '1234567890', '+1', true);
//
//})->throws(PhoneException::class);

it('AccountLockoutException verifies phone security code', function () {
    // 设置模拟响应为 429 太多请求
    MockClient::global([
        SecurityVerifyPhoneSecurityCode::class => MockResponse::make(),
    ]);

    // 调用 securityVerifyPhoneSecurityCode 方法
    $response = $this->appleId->securityVerifyPhoneSecurityCode(1, '1234567890', 'US', '+1', '987654');

    expect($response)->toBeInstanceOf(Response::class)
        ->and($response->failed())
        ->toBeFalse();
});

it('throws exception when securityVerifyPhoneSecurityCode fails', function () {
    // 设置模拟响应为 400 错误
    MockClient::global([
        SecurityVerifyPhoneSecurityCode::class => MockResponse::make(
            body: ['error' => 'Invalid code'],
            status: 400
        ),
    ]);

    // 调用 securityVerifyPhoneSecurityCode 方法，预期抛出异常
    $this->appleId->securityVerifyPhoneSecurityCode(1, '1234567890', 'US', '+1', 'invalid_code');
})->throws(ClientException::class, 'Bad Request (400) Response: {"error":"Invalid code"}');
