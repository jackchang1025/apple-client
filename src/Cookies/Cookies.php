<?php

namespace Apple\Client\Cookies;

use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SetCookie;
use Psr\SimpleCache\CacheInterface;

class Cookies extends CookieJar
{
    public function __construct(
        protected readonly string $clientId,
        protected readonly CacheInterface $cache,
        protected readonly int $cookieCacheTtl = 3600,
        protected readonly bool $storeSessionCookies = true,
    ) {
        parent::__construct();
        $this->load();
    }

    public function load(): void
    {
        $cookies = $this->cache->get($this->getCacheKey());

        if (!empty($cookies)) {
            foreach ($cookies as $cookie) {
                $this->setCookie(new SetCookie($cookie));
            }
        }
    }

    public function save(?int $cookieCacheTtl = null): void
    {
        $json = [];
        /** @var SetCookie $cookie */
        foreach ($this as $cookie) {
            if ($this->storeSessionCookies || !$cookie->getDiscard()) {
                $json[] = $cookie->toArray();
            }
        }

        $this->cache->set($this->getCacheKey(), $json, $cookieCacheTtl ?? $this->cookieCacheTtl);
    }

    public function getCacheKey(): string
    {
        return sprintf("cookie:%s", $this->clientId);
    }

    public function __destruct()
    {
        $this->save();
    }

    public function toString(): string
    {
        return implode('; ', array_map(function (SetCookie $cookie) {
            return $cookie->getName() . '=' . $cookie->getValue();
        }, $this->getIterator()->getArrayCopy()));
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}