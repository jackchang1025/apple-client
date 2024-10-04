<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Apple\Client\Integrations\AppleId;

use Apple\Client\AppleId;
use Apple\Client\Config\Config;
use Apple\Client\Integrations\AppleConnector;

class AppleIdConnector extends AppleConnector
{
    use AppleId;

    public function resolveBaseUrl(): string
    {
        return 'https://appleid.apple.com';
    }

    public function defaultPersistentHeaders(): array
    {
        return ['scnt'];
    }

    public function getAppleIdConnector(): AppleIdConnector
    {
        return $this;
    }

    protected function defaultHeaders(): array
    {
        /**
         * @var Config $config
         */
        $config = $this->config();

        return [
            'Connection' => 'Keep-Alive',
            'Content-Type' => 'application/json',
            'Accept' => 'application/json, text/plain, */*',
            'Accept-Language' => 'zh-CN,en;q=0.9,zh;q=0.8',
            'X-Apple-I-Request-Context' => 'ca',
            'X-Apple-I-TimeZone' => 'Asia/Shanghai',
            'Sec-Fetch-Site' => 'same-origin',
            'Sec-Fetch-Mode' => 'cors',
            'Sec-Fetch-Dest' => 'empty',
            'referer' => $this->resolveBaseUrl(),
            'host' => 'appleid.apple.com',
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36',
            'x-apple-i-fd-client-info' => [
                "U" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36",
                "L" => $config->getLocale(),
                "Z" => "GMT+02:00",
                "V" => "1.1",
                "F" => "",
            ],
        ];
    }
}
