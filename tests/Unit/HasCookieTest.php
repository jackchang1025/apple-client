<?php

namespace Tests\Unit;

use Apple\Client\Cookies\HasCookie;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SetCookie;
use GuzzleHttp\RequestOptions;
use Mockery;
use Saloon\Http\PendingRequest;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Saloon\Contracts\Body\HasBody;

class HasCookieTest
{
    use HasCookie;
}

beforeEach(function () {
    $this->hasCookie = new HasCookieTest();
    $this->mockClient = new MockClient();
    $this->pendingRequest = Mockery::mock(PendingRequest::class)->makePartial();
});

it('initializes without a cookie jar', function () {
    expect($this->hasCookie->getCookieJar())->toBeNull();
});

it('sets and gets cookie jar', function () {
    $cookieJar = new CookieJar();
    $this->hasCookie->setCookieJar($cookieJar);

    expect($this->hasCookie->getCookieJar())->toBe($cookieJar);
});

it('boots HasCookie with cookie jar', function () {
    $cookieJar = new CookieJar();
    $this->hasCookie->setCookieJar($cookieJar);

    $this->hasCookie->bootHasCookie($this->pendingRequest);

    expect($this->pendingRequest->config()->get(RequestOptions::COOKIES))->toBe($cookieJar);
});

it('boots HasCookie without cookie jar', function () {
    $this->hasCookie->bootHasCookie($this->pendingRequest);

    expect($this->pendingRequest->config()->get(RequestOptions::COOKIES))->toBeNull();
});

it('maintains existing config when booting HasCookie', function () {
    $this->pendingRequest->config()->add('existing_key', 'existing_value');
    $cookieJar = new CookieJar();
    $this->hasCookie->setCookieJar($cookieJar);

    $this->hasCookie->bootHasCookie($this->pendingRequest);

    expect($this->pendingRequest->config()->get('existing_key'))->toBe('existing_value')
        ->and($this->pendingRequest->config()->get(RequestOptions::COOKIES))->toBe($cookieJar);
});

it('allows setting cookie jar to null', function () {
    $cookieJar = new CookieJar();
    $this->hasCookie->setCookieJar($cookieJar);
    expect($this->hasCookie->getCookieJar())->toBe($cookieJar);

    $this->hasCookie->setCookieJar(null);
    expect($this->hasCookie->getCookieJar())->toBeNull();
});

//it('preserves cookies across multiple requests', function () {
//    $cookieJar = new CookieJar();
//    $cookie = new SetCookie(['Name' => 'test_cookie', 'Value' => 'test_value', 'Domain' => 'example.com']);
//    $cookieJar->setCookie($cookie);
//
//    $this->hasCookie->setCookieJar($cookieJar);
//
//    // 设置特定的模拟响应
//    MockClient::global([
//        'https://example.com' => MockResponse::make(headers:['Set-Cookie' => 'new_cookie=new_value']),
//    ]);
//
//    $this->hasCookie->bootHasCookie($this->pendingRequest);
//    $response = $this->pendingRequest->send();
//
//    expect($cookieJar->getCookieByName('test_cookie')?->getValue())->toBe('test_value')
//        ->and($cookieJar->getCookieByName('new_cookie')?->getValue())->toBe('new_value');
//});



it('does not modify PendingRequest when cookie jar is not set', function () {
    $originalConfig = $this->pendingRequest->config()->all();

    $this->hasCookie->bootHasCookie($this->pendingRequest);

    expect($this->pendingRequest->config()->all())->toBe($originalConfig);
});