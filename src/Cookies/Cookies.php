<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Weijiajia\Cookies;

use InvalidArgumentException;
use Psr\SimpleCache\CacheInterface;

class Cookies extends CookieJar
{
    public function __construct(
        protected readonly CacheInterface $cache,
        protected readonly string $key = '',
        protected readonly int $ttl = 3600,
        protected readonly bool $storeSessionCookies = true,
        bool $strictMode = false
    ) {
        parent::__construct($strictMode, $this->load());
    }

    /**
     * @throws InvalidArgumentException|\RuntimeException|\Psr\SimpleCache\InvalidArgumentException
     *
     * @return array
     */
    public function load(): array
    {
        $cookies = $this->cache->get($this->getCacheKey(), []);

        if (!is_array($cookies)) {
            throw new \RuntimeException('Cookie cache is not an array');
        }

        return $cookies;
    }

    /**
     * @param int|null $cookieCacheTtl
     *
     * @throws InvalidArgumentException
     *
     * @return void
     */
    public function save(?int $cookieCacheTtl = null): void
    {
        $json = [];

        /** @var SetCookie $cookie */
        foreach ($this as $cookie) {
            if ($this->storeSessionCookies || !$cookie->getDiscard()) {
                $json[] = $cookie->toArray();
            }
        }

        if (empty($json)) {
            return;
        }

        $this->cache->set($this->getCacheKey(), $json, $cookieCacheTtl ?? $this->ttl);
    }

    /**
     * @return string
     */
    public function getCacheKey(): string
    {
        return sprintf("cookie:%s", $this->key);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function __destruct()
    {
        $this->save();
    }
}
