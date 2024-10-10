<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Weijiajia\Cookies\CookieJar;
use Weijiajia\Cookies\SetCookie;
use Saloon\Http\PendingRequest;
use Saloon\Http\Response;

beforeEach(function () {
    $this->cookieJar = new CookieJar();
});

it('can be instantiated', function () {
    expect($this->cookieJar)->toBeInstanceOf(CookieJar::class);
});

it('can set and get a cookie', function () {
    $cookie = new SetCookie([
        'Name' => 'test_cookie',
        'Value' => 'test_value',
        'Domain' => 'example.com',
    ]);

    $this->cookieJar->setCookie($cookie);

    expect($this->cookieJar->getCookieByName('test_cookie'))
        ->toBeInstanceOf(SetCookie::class)
        ->and($this->cookieJar->getCookieByName('test_cookie')->getValue())
        ->toBe('test_value');
});

it('can clear all cookies', function () {
    $cookie1 = new SetCookie(['Name' => 'cookie1', 'Value' => 'value1', 'Domain' => 'example.com']);
    $cookie2 = new SetCookie(['Name' => 'cookie2', 'Value' => 'value2', 'Domain' => 'example.com']);

    $this->cookieJar->setCookie($cookie1);
    $this->cookieJar->setCookie($cookie2);

    expect($this->cookieJar->count())->toBe(2);

    $this->cookieJar->clear();

    expect($this->cookieJar->count())->toBe(0);
});

it('can clear cookies for a specific domain', function () {
    $cookie1 = new SetCookie(['Name' => 'cookie1', 'Value' => 'value1', 'Domain' => 'example.com']);
    $cookie2 = new SetCookie(['Name' => 'cookie2', 'Value' => 'value2', 'Domain' => 'test.com']);

    $this->cookieJar->setCookie($cookie1);
    $this->cookieJar->setCookie($cookie2);

    $this->cookieJar->clear('example.com');

    expect($this->cookieJar->count())->toBe(1)
        ->and($this->cookieJar->getCookieByName('cookie2'))->not->toBeNull();
});

it('can clear session cookies', function () {
    $sessionCookie = new SetCookie(['Name' => 'session', 'Value' => 'value', 'Domain' => 'example.com', 'Discard' => true]);
    $persistentCookie = new SetCookie(['Name' => 'persistent', 'Value' => 'value', 'Domain' => 'example.com', 'Expires' => time() + 3600]);

    $this->cookieJar->setCookie($sessionCookie);
    $this->cookieJar->setCookie($persistentCookie);

    $this->cookieJar->clearSessionCookies();

    expect($this->cookieJar->count())->toBe(1)
        ->and($this->cookieJar->getCookieByName('persistent'))->not->toBeNull();
});

it('can extract cookies from a response', function () {
    $pendingRequest = Mockery::mock(PendingRequest::class);
    $pendingRequest->shouldReceive('getUri->getHost')->andReturn('example.com');

    $response = Mockery::mock(Response::class);
    $response->shouldReceive('header')
        ->with('Set-Cookie')
        ->andReturn(['test_cookie=test_value; Domain=example.com; Path=/']);

    $this->cookieJar->extractCookies($pendingRequest, $response);

    expect($this->cookieJar->getCookieByName('test_cookie'))
        ->toBeInstanceOf(SetCookie::class)
        ->and($this->cookieJar->getCookieByName('test_cookie')->getValue())
        ->toBe('test_value');
});

it('can add cookies to a request', function () {
    $cookie = new SetCookie([
        'Name' => 'test_cookie',
        'Value' => 'test_value',
        'Domain' => 'example.com',
        'Path' => '/',
    ]);

    $this->cookieJar->setCookie($cookie);

    $pendingRequest = Mockery::mock(PendingRequest::class);
    $pendingRequest->shouldReceive('getUri->getScheme')->andReturn('https');
    $pendingRequest->shouldReceive('getUri->getHost')->andReturn('example.com');
    $pendingRequest->shouldReceive('getUri->getPath')->andReturn('/');
    $pendingRequest->shouldReceive('headers->add')
        ->with('Cookie', 'test_cookie=test_value')
        ->once();

    $this->cookieJar->withCookieHeader($pendingRequest);
});

it('does not add expired cookies to a request', function () {
    $expiredCookie = new SetCookie([
        'Name' => 'expired_cookie',
        'Value' => 'expired_value',
        'Domain' => 'example.com',
        'Path' => '/',
        'Expires' => time() - 3600, // 1 hour ago
    ]);

    $this->cookieJar->setCookie($expiredCookie);

    $pendingRequest = Mockery::mock(PendingRequest::class);
    $pendingRequest->shouldReceive('getUri->getScheme')->andReturn('https');
    $pendingRequest->shouldReceive('getUri->getHost')->andReturn('example.com');
    $pendingRequest->shouldReceive('getUri->getPath')->andReturn('/');
    $pendingRequest->shouldReceive('headers->add')->never();

    $this->cookieJar->withCookieHeader($pendingRequest);
});

it('can be converted to an array', function () {
    $cookie1 = new SetCookie(['Name' => 'cookie1', 'Value' => 'value1', 'Domain' => 'example.com']);
    $cookie2 = new SetCookie(['Name' => 'cookie2', 'Value' => 'value2', 'Domain' => 'example.com']);

    $this->cookieJar->setCookie($cookie1);
    $this->cookieJar->setCookie($cookie2);

    $array = $this->cookieJar->toArray();

    expect($array)->toBeArray()
        ->and($array)->toHaveCount(2)
        ->and($array[0])->toBeArray()
        ->and($array[0]['Name'])->toBe('cookie1')
        ->and($array[1]['Name'])->toBe('cookie2');
});
