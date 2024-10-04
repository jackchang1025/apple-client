<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Apple\Client;

use Apple\Client\Config\Config;
use Apple\Client\Cookies\Cookies;
use Apple\Client\Header\CacheStore;
use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;

readonly class AppleFactory
{
    public function __construct(
        protected CacheInterface $cache,
        protected LoggerInterface $logger,
    ) {
    }

    /**
     * @param string               $clientId
     * @param array<string, mixed> $config
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *
     * @return AppleClient
     */
    public function create(string $clientId, array $config = []): AppleClient
    {
        // 创建 Cookie 和 Header 仓库
        $cookieStore = new Cookies(
            cache: $this->cache,
            key: $clientId,
            ttl: 3600,
        );

        $headerStore = new CacheStore(
            cache: $this->cache,
            key: $clientId,
            ttl: 3600,
            defaultData: []
        );

        return new AppleClient(
            config: Config::fromArray($config),
            headerRepositories: $headerStore,
            cookieJar: $cookieStore,
            logger: $this->logger,
        );
    }
}
