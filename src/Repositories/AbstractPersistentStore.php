<?php

namespace Apple\Client\Repositories;

use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;
use Saloon\Repositories\ArrayStore;

abstract class AbstractPersistentStore extends ArrayStore
{
    public function __construct(
        protected readonly string $clientId,
        protected readonly CacheInterface $cache,
        protected readonly int $ttl = 3600,
        protected readonly string $prefix = 'store',
        array $defaultData = []
    ) {
        parent::__construct(array_merge($defaultData, $this->load()));
    }

    protected function load(): array
    {
        try {
            return $this->cache->get($this->getCacheKey(), []);
        } catch (InvalidArgumentException) {
            return [];
        }
    }


    protected function save(?int $cookieCacheTtl = null): void
    {
        try {
            $this->cache->set($this->getCacheKey(), $this->data, $cookieCacheTtl ?? $this->ttl);
        } catch (InvalidArgumentException $e) {
            // Handle or log the error
        }
    }

    protected function getCacheKey(): string
    {
        return sprintf("%s:%s", $this->prefix,$this->clientId);
    }

    public function __destruct()
    {
        $this->save();
    }
}