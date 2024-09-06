<?php

namespace Apple\Client\Middleware;

use Apple\Client\Cookies\CookieManagerInterface;
use GuzzleHttp\RequestOptions;
use Saloon\Http\PendingRequest;
use Saloon\Contracts\RequestMiddleware;

class CookiesMiddleware implements RequestMiddleware
{
    public function __invoke(PendingRequest $pendingRequest): PendingRequest
    {
        $cookieManager = $this->resolveCookieManager($pendingRequest);

        if ($cookieManager instanceof CookieManagerInterface && !$pendingRequest->config()->get(RequestOptions::COOKIES)) {
            $pendingRequest->config()
                ->add(RequestOptions::COOKIES, $cookieManager->getCookieJar());
        }

        return $pendingRequest;
    }

    private function resolveCookieManager(PendingRequest $pendingRequest): ?CookieManagerInterface
    {
        $request = $pendingRequest->getRequest();
        $connector = $pendingRequest->getConnector();

        if ($request instanceof CookieManagerInterface) {
            return $request;
        }

        if ($connector instanceof CookieManagerInterface) {
            return $connector;
        }

        return null;
    }
}