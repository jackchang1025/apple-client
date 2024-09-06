<?php

namespace Apple\Client;

use Apple\Client\Integrations\AppleId\AppleIdConnector;
use Apple\Client\Integrations\Idmsa\IdmsaConnector;
use Apple\Client\Repositories\HasRepositories;
use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;
use Saloon\Traits\Macroable;

class Apple
{
    use Macroable;
    use AppleId;
    use Idmsa;

    protected AppleIdConnector $appleIdConnector;
    protected IdmsaConnector $idmsaConnector;

    public function __construct(protected CacheInterface $cache,protected LoggerInterface $logger,protected string $clientId)
    {
        $this->appleIdConnector = new AppleIdConnector($cache,$this->logger,$clientId);
        $this->idmsaConnector = new IdmsaConnector($cache,$this->logger,$clientId);
    }

    public function getCache(): CacheInterface
    {
        return $this->cache;
    }

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function getAppleIdConnector(): AppleIdConnector
    {
        return $this->appleIdConnector;
    }

    public function getIdmsaConnector(): IdmsaConnector
    {
        return $this->idmsaConnector;
    }
}