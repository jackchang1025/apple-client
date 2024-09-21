<?php

/**
 * This file is part of the Your-Project-Name package.
 *
 * (c) Your Name <your-email@example.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Apple\Client\Cookies;

use GuzzleHttp\Cookie\CookieJarInterface;
use GuzzleHttp\RequestOptions;
use Saloon\Http\PendingRequest;

trait HasCookie
{
    protected ?CookieJarInterface $cookieJar = null;

    public function bootHasCookie(PendingRequest $pendingRequest): void
    {
        if ($cookieJar = $this->getCookieJar()) {

            $request = $pendingRequest->cookie();

            $cookieJar->withCookieHeader($request);
            
            $pendingRequest->config()
                ->add(RequestOptions::COOKIES, $this->getCookieJar());
        }
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
