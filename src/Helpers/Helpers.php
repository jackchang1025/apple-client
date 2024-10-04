<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Apple\Client\Helpers;

trait Helpers
{
    protected ?string $uuid = null;

    protected function buildUUid(): string
    {
        return $this->uuid ??= sprintf('auth-%s', uniqid('', true));
    }
}
