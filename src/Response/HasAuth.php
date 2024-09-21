<?php

/**
 * This file is part of the Your-Project-Name package.
 *
 * (c) Your Name <your-email@example.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Apple\Client\Response;

trait HasAuth
{
    /**
     * @throws \JsonException
     *
     * @return array|null
     */
    public function authorizeSing(): ?array
    {
        $document = $this->dom()
            ->filter('script[type="application/json"].boot_args')
            ->first();

        if (!$document->count()) {
            return null;
        }

        // 获取 script 标签的内容
        $jsonString = $document->text();

        // 解码 JSON 数据
        $data = json_decode($jsonString, true, 512, JSON_THROW_ON_ERROR);

        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('JSON解析错误: ' . json_last_error_msg());
        }

        return $data;
    }

    /**
     * @throws \JsonException
     *
     * @return bool
     */
    public function hasTrustedDevices(): bool
    {
        return data_get($this->authorizeSing(), 'direct.hasTrustedDevices', false);
    }
}
