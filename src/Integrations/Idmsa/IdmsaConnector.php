<?php

namespace Apple\Client\Integrations\Idmsa;

use Apple\Client\Integrations\AppleConnector;
use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;

class IdmsaConnector extends AppleConnector
{
    public function __construct(protected CacheInterface $cache,protected LoggerInterface $logger,protected string $token)
    {
    }

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    public function getCache(): CacheInterface
    {
        return $this->cache;
    }

    public function getClientId(): string
    {
        return $this->token;
    }

    public function resolveBaseUrl(): string
    {
        return 'https://idmsa.apple.com';
    }

    public function persistentHeaders(): array
    {
//        return [];
        return ['X-Apple-ID-Session-Id','X-Apple-Auth-Attributes','scnt'];
    }

    protected function defaultHeaders(): array
    {
        return [
            'X-Apple-Widget-Key'          => $this->appleConfig()->getServiceKey(),
            'X-Apple-OAuth-Redirect-URI'  => $this->appleConfig()->getApiUrl(),
            'X-Apple-OAuth-Client-Id'     => $this->appleConfig()->getServiceKey(),
            'X-Apple-OAuth-Client-Type'   => 'firstPartyAuth',
            'x-requested-with'            => 'XMLHttpRequest',
            'X-Apple-OAuth-Response-Mode' => 'web_message',
            'X-APPLE-HC'                  => '1:12:20240626165907:82794b5d498b7d7dc29740b23971ded5::4824',
            'X-Apple-Domain-Id'           => '1',
            'Origin'                      => $this->resolveBaseUrl(),
            'Referer'                     => $this->resolveBaseUrl(),
            'Accept'                      => 'application/json, text/javascript, */*; q=0.01',
            'Accept-Language'             => 'zh-CN,en;q=0.9,zh;q=0.8',
            'User-Agent'                  => 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.198 Safari/537.36',
            'Content-Type'                => 'application/json',
            'Priority'                    => 'u=1, i',
            'Sec-Ch-Ua'                   => "Chromium;v=124, Google Chrome;v=124",
            'Sec-Ch-Ua-Mobile'            => '?0',
            'Sec-Ch-Ua-Platform'          => 'Windows',
            'Connection'                  => 'Keep-Alive',
            'X-Apple-I-TimeZone'          => 'Asia/Shanghai',
            'Sec-Fetch-Site'              => 'same-origin',
            'Sec-Fetch-Mode'              => 'cors',
            'Sec-Fetch-Dest'              => 'empty',
        ];
    }
}