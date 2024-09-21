<?php

/**
 * This file is part of the Your-Project-Name package.
 *
 * (c) Your Name <your-email@example.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Apple\Client\DataConstruct;

use Carbon\Carbon;

class PhoneCodeResponse
{
    public function __construct(protected int $status, protected ?string $code = null, protected ?string $message = null, protected ?Carbon $expire = null)
    {
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function getExpire(): ?Carbon
    {
        return $this->expire;
    }

    public function isExpire(): bool
    {
        if (null === $this->expire) {
            return false;
        }

        //判断当前时间和 $expire 是否相差 10 分钟
        return Carbon::now()->diffInMinutes($this->expire) > 10;
    }
}
