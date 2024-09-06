<?php

namespace Apple\Client\Integrations;

use Apple\Client\Cookies\CookieManagement;
use Apple\Client\Header\HasHeaderStore;
use Apple\Client\Logger\Logger;
use Apple\Client\Repositories\HasRepositories;
use Apple\Client\Trait\HasAppleConfig;
use Saloon\Http\Connector;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Request;
use Saloon\Traits\Plugins\AlwaysThrowOnErrors;
use Saloon\Traits\Plugins\HasTimeout;

abstract class AppleConnector extends Connector
{
    use HasTimeout;
    use CookieManagement;
    use HasAppleConfig;
    use HasHeaderStore;
    use AlwaysThrowOnErrors;
    use Logger;
    use HasRepositories;

    public function defaultConfig(): array
    {
        return [
            'verify' => false,
        ];
    }

    public function resolveResponseClass(): string
    {
        return Response::class;
    }

    /**
     * @param Request $request
     * @param MockClient|null $mockClient
     * @param callable|null $handleRetry
     * @return Response
     * @throws \Saloon\Exceptions\Request\FatalRequestException
     * @throws \Saloon\Exceptions\Request\RequestException
     */
    public function send(Request $request, MockClient $mockClient = null, callable $handleRetry = null): Response
    {
        /**
         * @var Response $response
         */
        $response = parent::send($request, $mockClient, $handleRetry);

        return $response;
    }
}