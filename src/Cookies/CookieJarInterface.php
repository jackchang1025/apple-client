<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Weijiajia\Cookies;

use Saloon\Http\PendingRequest;
use Saloon\Http\Response;

interface CookieJarInterface extends \Countable, \IteratorAggregate
{
    public function withCookieHeader(PendingRequest $request): PendingRequest;

    public function extractCookies(PendingRequest $request, Response $response): void;

    public function setCookie(SetCookie $cookie): bool;

    public function clear(?string $domain = null, ?string $path = null, ?string $name = null): void;

    public function clearSessionCookies(): void;

    public function toArray(): array;
}
