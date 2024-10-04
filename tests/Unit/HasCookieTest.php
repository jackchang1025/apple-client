<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Unit;

use Apple\Client\Cookies\CookieJar;
use Apple\Client\Cookies\HasCookie;
use Mockery;
use Saloon\Helpers\MiddlewarePipeline;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\PendingRequest;

class HasCookieTest
{
    use HasCookie;
}

beforeEach(function () {
    $this->hasCookie = new HasCookieTest();
    $this->mockClient = new MockClient();
    $this->middlewarePipeline = Mockery::mock(MiddlewarePipeline::class);
    $this->pendingRequest = Mockery::mock(PendingRequest::class)->makePartial();
    $this->pendingRequest->shouldReceive('getConnector->middleware')->andReturn($this->middlewarePipeline);
});

it('initializes without a cookie jar', function () {
    expect($this->hasCookie->getCookieJar())->toBeNull();
});

it('sets and gets cookie jar', function () {
    $cookieJar = new CookieJar();
    $this->hasCookie->setCookieJar($cookieJar);

    expect($this->hasCookie->getCookieJar())->toBe($cookieJar);
});
