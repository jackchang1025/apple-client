<?php

namespace Apple\Client\DataConstruct;

readonly class Phone
{
    public function __construct(private array $data)
    {
    }

    /**
     * 获取电话号码 ID
     */
    public function getId(): ?int
    {
        return $this->data['id'] ?? null;
    }

    /**
     * 获取带有区号的完整电话号码（部分隐藏）
     */
    public function getNumberWithDialCode(): ?string
    {
        return $this->data['numberWithDialCode'] ?? null;
    }

    /**
     * 获取电话号码的推送模式（如 SMS）
     */
    public function getPushMode(): ?string
    {
        return $this->data['pushMode'] ?? null;
    }

    /**
     * 获取混淆后的电话号码
     */
    public function getObfuscatedNumber(): ?string
    {
        return $this->data['obfuscatedNumber'] ?? null;
    }

    /**
     * 获取电话号码的最后两位数字
     */
    public function getLastTwoDigits(): ?string
    {
        return $this->data['lastTwoDigits'] ?? null;
    }
}
