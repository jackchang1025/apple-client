<?php

/**
 * This file is part of the Your-Project-Name package.
 *
 * (c) Your Name <your-email@example.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Apple\Client\Integrations\AppleAuth;

use Apple\Client\AppleAuth;
use Apple\Client\AppleClient;
use Apple\Client\Integrations\AppleConnector;

class AppleAuthConnector extends AppleConnector
{
    use AppleAuth;
    protected ?array $options;

    public function __construct(protected AppleClient $apple)
    {
        $this->options = $this->apple->config()
            ->get('apple_auth', []);

        if (empty($this->options['url'])) {
            throw new \InvalidArgumentException('apple_auth config is empty');
        }

        parent::__construct($apple);
    }

    public function getAppleAuthConnector(): AppleAuthConnector
    {
        return $this;
    }

    public function defaultConfig(): array
    {
        return $this->options['config'] ?? [];
    }

    public function resolveBaseUrl(): string
    {
        return $this->options['url'];
    }

    protected function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json, text/plain, */*',
            'Accept-Language' => 'zh-CN,en;q=0.9,zh;q=0.8',
        ];
    }
}
