<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Weijiajia\Integrations;

use Weijiajia\AppleClient;
use Weijiajia\Config\HasConfig;
use Weijiajia\Cookies\CookieJarInterface;
use Weijiajia\Cookies\HasCookie;
use Weijiajia\Header\HasHeaderSynchronize;
use Weijiajia\Header\HasPersistentHeaders;
use Weijiajia\Helpers\Helpers;
use Weijiajia\Logger\Logger;
use Weijiajia\Proxy\HasProxy;
use Weijiajia\Response\Response;
use Psr\Log\LoggerInterface;
use Saloon\Contracts\ArrayStore;
use Saloon\Http\Connector;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Request;
use Saloon\Traits\Plugins\AlwaysThrowOnErrors;
use Saloon\Traits\Plugins\HasTimeout;
use Weijiajia\Trait\HasTries;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;


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
    use HasTries;
    use HasConfig {
        HasConfig::config as baseConfig;
    }

    public function __construct(protected AppleClient $apple)
    {
        $this->setTries(3);
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

    public function handleRetry(FatalRequestException|RequestException $exception, Request $request): bool
    {
        $handleRetry = $this->getHandleRetry() ?? $this->apple->getHandleRetry() ?? static fn (): bool => true;

       return $handleRetry($exception,$request);
    }
}
