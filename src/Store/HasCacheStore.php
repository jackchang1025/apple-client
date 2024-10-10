<?php

namespace Weijiajia\Store;

trait HasCacheStore
{
    protected static ?CacheStore $cacheStore;

    public function withCacheStore(?CacheStore $cacheStore): static
    {
        self::$cacheStore = $cacheStore;

        return $this;
    }

    public function getCacheStore(): ?CacheStore
    {
        return self::$cacheStore;
    }
}