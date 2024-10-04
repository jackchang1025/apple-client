<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Feature;

use Apple\Client\Cookies\CookieJar;
use Apple\Client\Cookies\HasCookie;
use Apple\Client\Cookies\SetCookie;
use Saloon\Enums\Method;
use Saloon\Http\Connector;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Saloon\Http\Request;

class CookieSyncTest extends Connector
{
    use HasCookie;

    protected string $method = 'GET';

    public function resolveBaseUrl(): string
    {
        return 'https://example.com/api';
    }
}

class CookieSyncTestRequest extends Request
{
    use HasCookie;

    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/example';
    }
}

beforeEach(function () {
    $this->testConnector = new CookieSyncTest();
    $this->testRequest = new CookieSyncTestRequest();
});
//
it('syncs cookies across multiple requests', function () {
    $cookieJar = new CookieJar();
    $this->testConnector->setCookieJar($cookieJar);

    // 设置特定的模拟响应
    MockClient::global([
        CookieSyncTestRequest::class => MockResponse::make(
            headers:[
            'Set-Cookie' => [
                'session1=abc123; Domain=example.com; Path=/',
                'session2=updated456; Domain=example.com; Path=/',
                'session3=updated456; Domain=example123.com; Path=/',
            ],
        ]
        ),
    ]);

    // First request: should set the initial cookie
    $response1 = $this->testConnector->send($this->testRequest);

    expect($cookieJar->getCookieByName('session1')?->getValue())->toBe('abc123')
        ->and($this->testConnector->getCookieJar()?->getCookieByName('session2')?->getValue())->toBe('updated456')
        ->and($this->testConnector->getCookieJar()?->getCookieByName('session3')?->getValue())->toBeNull();
});

it('includes cookies in subsequent requests', function () {
    // First request to set the cookie
    $this->cookieJar = new CookieJar(cookieArray:[
        new SetCookie([
            'Name' => 'session1',
            'Value' => '123',
            'Domain' => 'example.com',
            'Path' => '/',
        ]),
        [
            'Name' => 'session2',
            'Value' => '456',
            'Domain' => 'example.com',
            'Path' => '/',
        ],
        [
            'Name' => 'session3',
            'Value' => 'abc123',
            'Domain' => 'example2343242.com',
            'Path' => '/',
        ],
    ]);

    $this->testConnector->setCookieJar($this->cookieJar);

    // 设置特定的模拟响应
    MockClient::global(
        [
        CookieSyncTestRequest::class => MockResponse::make(body:['test' => 'response']),
        ]
    );

    $response = $this->testConnector->send($this->testRequest);

    // 确保请求中包含 cookie
    expect($response->json())->toBe(['test' => 'response'])
        ->and($response->getPendingRequest()->headers()->get('Cookie'))->toBe('session1=123; session2=456');
});
