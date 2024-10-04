<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Apple\Client\Integrations;

use Apple\Client\AppleClient;
use Apple\Client\Config\HasConfig;
use Apple\Client\Cookies\CookieJarInterface;
use Apple\Client\Cookies\HasCookie;
use Apple\Client\Header\HasHeaderSynchronize;
use Apple\Client\Header\HasPersistentHeaders;
use Apple\Client\Helpers\Helpers;
use Apple\Client\Logger\Logger;
use Apple\Client\Proxy\HasProxy;
use Apple\Client\Response\Response;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Str;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Saloon\Contracts\ArrayStore;
use Saloon\Http\Connector;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\PendingRequest;
use Saloon\Http\Request;
use Saloon\Traits\Plugins\AlwaysThrowOnErrors;
use Saloon\Traits\Plugins\HasTimeout;

abstract class AppleConnector extends Connector
{
    use HasTimeout;
    use HasCookie;
    use HasHeaderSynchronize;
    use AlwaysThrowOnErrors;
    use HasPersistentHeaders;
    use Logger;
    use HasProxy;
    use HasConfig;
    use Helpers;
    use HasConfig {
        HasConfig::config as baseConfig;
    }

    public function __construct(protected AppleClient $apple)
    {
    }

    public function getProxy(): ?string
    {
        return $this->proxy ?? $this->apple->getProxy();
    }

    public function getApple(): AppleClient
    {
        return $this->apple;
    }

    public function config(): ArrayStore
    {
        return $this->apple->config()
            ->merge($this->baseConfig()->all());
    }

    public function getHeaderRepositories(): ?ArrayStore
    {
        return $this->getHeaderRepositories ?? $this->apple->getHeaderRepositories();
    }

    public function getCookieJar(): ?CookieJarInterface
    {
        return $this->cookieJar ?? $this->apple->getCookieJar();
    }

    public function getLogger(): ?LoggerInterface
    {
        return $this->logger ?? $this->apple->getLogger();
    }

    public function resolveResponseClass(): string
    {
        return Response::class;
    }

    /**
     * @param Request         $request
     * @param MockClient|null $mockClient
     * @param callable|null   $handleRetry
     *
     * @throws \Saloon\Exceptions\Request\FatalRequestException
     * @throws \Saloon\Exceptions\Request\RequestException
     *
     * @return Response
     */
    public function send(Request $request, MockClient $mockClient = null, callable $handleRetry = null): Response
    {
        /**
         * @var Response $response
         */
        $response = parent::send($request, $mockClient, $handleRetry);

        return $response;
    }

    public function defaultRequestMiddle(PendingRequest $pendingRequest): \Closure
    {
        /**
         * @var AppleConnector $connector
         */
        $connector = $pendingRequest->getConnector();

        $config = $connector->getApple()->config();

        $pendingRequest->config()->get(RequestOptions::PROXY);

        return function (RequestInterface $request) use ($config) {
            $this->getLogger()
                ?->debug('request', [
                    'account' => $config->get('account')?->account,
                    'method' => $request->getMethod(),
                    'uri' => (string) $request->getUri(),
                    'headers' => $request->getHeaders(),
                    'body' => (string) $request->getBody(),
                ]);

            return $request;
        };
    }

    public function defaultResponseMiddle(PendingRequest $pendingRequest): \Closure
    {
        /**
         * @var AppleConnector $connector
         */
        $connector = $pendingRequest->getConnector();

        $config = $connector->getApple()->config();

        return function (ResponseInterface $response) use ($config) {
            $contentType = $response->getHeaderLine('Content-Type');

            if ($contentType !== 'text/html;charset=UTF-8') {
                $body = (string) $response->getBody();

                if (Str::length($body) > 2000) {
                    $body = Str::substr($body, 0, 2000);
                }

                $this->getLogger()?->info('response', [
                    'account' => $config->get('account')?->account,
                    'status' => $response->getStatusCode(),
                    'headers' => $response->getHeaders(),
                    'body' => $body,
                ]);
            }

            return $response;
        };
    }
}
