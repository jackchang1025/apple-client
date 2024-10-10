<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Weijiajia\DataConstruct;

readonly class ServiceError
{
    public function __construct(private array $data)
    {
    }

    public function getCode(): ?string
    {
        return $this->data['code'] ?? null;
    }

    public function getTitle(): ?string
    {
        return $this->data['title'] ?? null;
    }

    public function getMessage(): ?string
    {
        return $this->data['message'] ?? null;
    }

    public function getSuppressDismissal(): ?bool
    {
        return $this->data['suppressDismissal'] ?? null;
    }
}
