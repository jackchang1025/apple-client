<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Apple\Client\Cookies;

use Saloon\Http\PendingRequest;
use Saloon\Http\Response;

trait HasCookie
{
    protected ?CookieJarInterface $cookieJar = null;

    public function bootHasCookie(PendingRequest $pendingRequest): void
    {
        $pendingRequest->getConnector()
            ->middleware()
            ->onRequest(fn (PendingRequest $request) => $this->getCookieJar()?->withCookieHeader($request));

        $pendingRequest->getConnector()
            ->middleware()
            ->onResponse(fn (Response $response) => $this->getCookieJar()?->extractCookies($pendingRequest, $response));
    }

    public function setCookieJar(?CookieJarInterface $cookieJar): void
    {
        $this->cookieJar = $cookieJar;
    }

    public function getCookieJar(): ?CookieJarInterface
    {
        return $this->cookieJar;
    }
}
