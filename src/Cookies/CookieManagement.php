<?php

namespace Apple\Client\Cookies;

use Apple\Client\Repositories\HasAbstractPersistentStore;
use GuzzleHttp\Cookie\CookieJarInterface;
use GuzzleHttp\RequestOptions;
use Saloon\Http\PendingRequest;

trait CookieManagement
{
    use HasAbstractPersistentStore;

    protected static ?CookieJarInterface $cookieJar = null;

    public function cookieCacheTtl(): int
    {
        return 3600;
    }

    public function storeSessionCookies(): bool
    {
        return true;
    }

    public function bootCookieManagement(PendingRequest $pendingRequest): void
    {
        $pendingRequest->config()
            ->add(RequestOptions::COOKIES, $this->getCookieJar());
    }

    public function getCookieJar(): CookieJarInterface
    {
        return self::$cookieJar ?? new Cookies(
            clientId: $this->getClientId(),
            cache: $this->getCache(),
            cookieCacheTtl: $this->cookieCacheTtl(),
            storeSessionCookies: $this->storeSessionCookies()
        );
    }
}