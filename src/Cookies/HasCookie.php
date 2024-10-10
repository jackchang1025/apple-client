<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Weijiajia\Cookies;

use Saloon\Http\PendingRequest;
use Saloon\Http\Response;
use Weijiajia\Trait\HasPipeline;

trait HasCookie
{
    use HasPipeline;

    protected ?CookieJarInterface $cookieJar = null;

    public function bootHasCookie(PendingRequest $pendingRequest): void
    {
        if(!$this->requestPipelineExists($pendingRequest,'withCookieHeader')){
            $pendingRequest->getConnector()
                ->middleware()
                ->onRequest(function (PendingRequest $pendingRequest){
                    return $this->getCookieJar()?->withCookieHeader($pendingRequest);

                },'withCookieHeader');

        }

        if(!$this->responsePipelineExists($pendingRequest,'extractCookies')){
            $pendingRequest->getConnector()
                ->middleware()
                ->onResponse(fn (Response $response) => $this->getCookieJar()?->extractCookies($pendingRequest, $response),'extractCookies');
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
