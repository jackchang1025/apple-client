<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Weijiajia\Store;

use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;
use RuntimeException;
use Saloon\Repositories\ArrayStore;
use Saloon\Traits\Conditionable;

class CacheStore extends ArrayStore
{
    use Conditionable;

    /**
     * @param CacheInterface $cache
     * @param string         $key
     * @param int            $ttl
     * @param array          $defaultData
     *
     * @throws InvalidArgumentException
     */
    public function __construct(
        protected readonly CacheInterface $cache,
        protected readonly string $key = '',
        protected readonly int $ttl = 3600,
        protected readonly string $prx = "stores",
        protected readonly array $defaultData = []
    ) {
        parent::__construct(array_merge($defaultData, $this->load()));
    }

    /**
     * @throws InvalidArgumentException|RuntimeException
     *
     * @return array
     */
    public function load(): array
    {
        $data = $this->cache->get($this->getCacheKey(), []);

        if (!is_array($data)) {
            throw new RuntimeException('Cache data is not an array');
        }

        return $data;
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
        $this->cache->set($this->getCacheKey(), $this->data, $cookieCacheTtl ?? $this->ttl);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function __destruct()
    {
        $this->save();
    }

    protected function getCacheKey(): string
    {
        return sprintf("{$this->prx}:%s", $this->key);
    }
}
