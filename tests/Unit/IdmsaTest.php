<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Apple\Client\AppleClient;
use Apple\Client\Config\Config;
use Apple\Client\Config\HasConfig;
use Apple\Client\Exception\VerificationCodeException;
use Apple\Client\Helpers\Helpers;
use Apple\Client\Idmsa;
use Apple\Client\Integrations\Idmsa\IdmsaConnector;
use Apple\Client\Integrations\Idmsa\Request\AppleAuth\Auth;
use Apple\Client\Integrations\Idmsa\Request\AppleAuth\AuthorizeComplete;
use Apple\Client\Integrations\Idmsa\Request\AppleAuth\AuthorizeSing;
use Apple\Client\Integrations\Idmsa\Request\AppleAuth\AuthRepairComplete;
use Apple\Client\Integrations\Idmsa\Request\AppleAuth\SendPhoneSecurityCode;
use Apple\Client\Integrations\Idmsa\Request\AppleAuth\SendTrustedDeviceSecurityCode;
use Apple\Client\Integrations\Idmsa\Request\AppleAuth\Signin;
use Apple\Client\Integrations\Idmsa\Request\AppleAuth\SigninInit;
use Apple\Client\Integrations\Idmsa\Request\AppleAuth\VerifyPhoneSecurityCode;
use Apple\Client\Integrations\Idmsa\Request\AppleAuth\VerifyTrustedDeviceSecurityCode;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Saloon\Exceptions\Request\RequestException;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Saloon\Http\Response;

uses(MockeryPHPUnitIntegration::class);
uses()->group('idmsa');

class IdmsaTest
{
    use Idmsa;
    use HasConfig;
    use Helpers;

    protected IdmsaConnector $idmsaConnector;

    public function __construct(IdmsaConnector $connector)
    {
        $this->idmsaConnector = $connector;
    }

    public function getIdmsaConnector(): IdmsaConnector
    {
        return $this->idmsaConnector;
    }
}

beforeEach(function () {
    $this->config = new Config([
        'apple_auth' => [
            'url' => 'https://idmsa.apple.com',
        ],
    ]);

    $appleClient = new AppleClient($this->config);
    $this->connector = new IdmsaConnector($appleClient);
    $this->idmsa = new IdmsaTest($this->connector);
});

it('successfully initializes signin', function () {
    // 设置特定的模拟响应
    MockClient::global([
        SigninInit::class => MockResponse::make(
            body: [
                'salt' => 'test_salt',
                'b' => 'test_b',
                'c' => 'test_c',
                'iteration' => 1000,
                'protocol' => 'test_protocol',
            ]
        ),
    ]);

    $response = $this->idmsa->init('test_a', 'test@example.com');

    expect($response)->toBeInstanceOf(Response::class)
        ->and($response->json('salt'))->toBe('test_salt')
        ->and($response->json('b'))->toBe('test_b')
        ->and($response->json('c'))->toBe('test_c')
        ->and($response->json('iteration'))->toBe(1000)
        ->and($response->json('protocol'))->toBe('test_protocol')
        ->and($response->successful())->toBeTrue();
});

it('throws exception when init response is missing required fields', function () {
    // 设置特定的模拟响应
    MockClient::global([
        SigninInit::class => MockResponse::make(
            body: [
                'salt' => 'test_salt',
            ]
        ),
    ]);

    $this->idmsa->init('test_a', 'test@example.com');
})->throws(InvalidArgumentException::class);

it('successfully completes authorization', function () {
    // 设置特定的模拟响应
    MockClient::global([
        AuthorizeComplete::class => MockResponse::make(status: 409),
    ]);

    $response = $this->idmsa->complete('test@example.com', 'm1', 'm2', 'c', true);

    expect($response)->toBeInstanceOf(Response::class)
        ->and($response->successful())->toBeFalse();
});

it('fails when completes authorization', function () {
    // 设置特定的模拟响应
    MockClient::global([
        AuthorizeComplete::class => MockResponse::make(),
    ]);

    $this->idmsa->complete('test@example.com', 'm1', 'm2', 'c', true);
})->throws(RequestException::class);

it('successfully signs in', function () {
    // 设置特定的模拟响应
    MockClient::global([
        Signin::class => MockResponse::make(),
    ]);

    $response = $this->idmsa->sign();

    expect($response)->toBeInstanceOf(Response::class)
        ->and($response->successful())->toBeTrue();
});

it('successfully authorizes sing', function () {
    // 设置特定的模拟响应
    MockClient::global([
        AuthorizeSing::class => MockResponse::make(status: 409),
    ]);

    $response = $this->idmsa->authorizeSing('test@example.com', 'password', true);

    expect($response)->toBeInstanceOf(Response::class)
        ->and($response->successful())->toBeFalse();
});

it('fails when authorizes sing', function () {
    // 设置特定的模拟响应
    MockClient::global([
        AuthorizeSing::class => MockResponse::make(status: 200),
    ]);

    $this->idmsa->authorizeSing('test@example.com', 'password', true);
})->throws(RequestException::class);

it('successfully authenticates', function () {
    // 设置特定的模拟响应
    MockClient::global([
        Auth::class => MockResponse::make(),
    ]);

    $response = $this->idmsa->auth();

    expect($response)->toBeInstanceOf(Response::class)
        ->and($response->successful())->toBeTrue();
});

it('successfully verifies security code', function () {
    // 设置特定的模拟响应
    MockClient::global([
        VerifyTrustedDeviceSecurityCode::class => MockResponse::make(),
    ]);

    $response = $this->idmsa->verifySecurityCode('123456');

    expect($response)->toBeInstanceOf(Response::class)
        ->and($response->successful())->toBeTrue();
});

it('throws VerificationCodeException when security code verification fails', function () {
    // 设置特定的模拟响应
    MockClient::global([
        VerifyTrustedDeviceSecurityCode::class => MockResponse::make(
            body: [
                'error' => 'Invalid code',
            ],
            status: 400
        ),
    ]);

    $this->idmsa->verifySecurityCode('123456')->throw();
})->throws(VerificationCodeException::class);

it('successfully verifies phone code', function () {
    // 设置特定的模拟响应
    MockClient::global([
        VerifyPhoneSecurityCode::class => MockResponse::make([
        ], status: 200),
    ]);

    $response = $this->idmsa->verifyPhoneCode('test_id', '123456');

    expect($response)->toBeInstanceOf(Response::class)
        ->and($response->successful())->toBeTrue();
});

it('throws VerificationCodeException when phone code verification fails', function () {
    // 设置特定的模拟响应
    MockClient::global([
        VerifyPhoneSecurityCode::class => MockResponse::make([
            'error' => 'Invalid code',
        ], status: 400),
    ]);

    $this->idmsa->verifyPhoneCode('test_id', '123456');
})->throws(VerificationCodeException::class);

it('successfully sends security code', function () {
    // 设置特定的模拟响应
    MockClient::global([
        SendTrustedDeviceSecurityCode::class => MockResponse::make([
        ]),
    ]);

    $response = $this->idmsa->sendSecurityCode();

    expect($response)->toBeInstanceOf(Response::class)
        ->and($response->successful())->toBeTrue();
});

it('successfully sends phone security code', function () {
    // 设置特定的模拟响应
    MockClient::global([
        SendPhoneSecurityCode::class => MockResponse::make([
        ]),
    ]);

    $response = $this->idmsa->sendPhoneSecurityCode(123);

    expect($response)->toBeInstanceOf(Response::class)
        ->and($response->successful())->toBeTrue();
});

it('successfully manages privacy accept', function () {
    // 设置特定的模拟响应
    MockClient::global([
        AuthRepairComplete::class => MockResponse::make([
        ]),
    ]);

    $response = $this->idmsa->managePrivacyAccept();

    expect($response)->toBeInstanceOf(Response::class)
        ->and($response->successful())->toBeTrue();
});
