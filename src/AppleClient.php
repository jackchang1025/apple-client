<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Weijiajia;

use Saloon\Traits\Conditionable;
use Weijiajia\Config\HasConfig;
use Weijiajia\Cookies\CookieJarInterface;
use Weijiajia\Cookies\HasCookie;
use Weijiajia\Header\HasHeaderSynchronize;
use Weijiajia\Helpers\Helpers;
use Weijiajia\Integrations\AppleAuth\AppleAuthConnector;
use Weijiajia\Integrations\AppleId\AppleIdConnector;
use Weijiajia\Integrations\Idmsa\IdmsaConnector;
use Weijiajia\Logger\Logger;
use Weijiajia\Proxy\HasProxy;
use Psr\Log\LoggerInterface;
use Saloon\Contracts\ArrayStore as ArrayStoreContract;
use Saloon\Traits\Macroable;
use Weijiajia\Store\CacheStore;
use Weijiajia\Store\HasCacheStore;
use Weijiajia\Trait\HasTries;

class AppleClient
{
    use Macroable;
    use AppleId;
    use Idmsa;
    use AppleAuth;
    use HasConfig;
    use HasProxy;
    use HasCookie;
    use HasHeaderSynchronize;
    use Helpers;
    use Logger;
    use Conditionable;
    use HasCacheStore;
    use HasTries;

    protected AppleIdConnector $appleIdConnector;
    protected IdmsaConnector $idmsaConnector;
    protected AppleAuthConnector $appleAuthConnector;


    /**
     * @param ArrayStoreContract $config
     * @param ArrayStoreContract|null $headerRepositories
     * @param CookieJarInterface|null $cookieJar
     * @param LoggerInterface|null $logger
     * @param CacheStore|null $cacheStore
     */
    public function __construct(
        ArrayStoreContract $config,
        ?ArrayStoreContract $headerRepositories = null,
        ?CookieJarInterface $cookieJar = null,
        ?LoggerInterface $logger = null,
        ?CacheStore $cacheStore = null,
    ) {
        $this->withConfig($config);
        $this->withCacheStore($cacheStore);
        $this->setLogger($logger);
        $this->setHeaderRepositories($headerRepositories);
        $this->setCookieJar($cookieJar);

        $this->appleIdConnector = new AppleIdConnector($this);
        $this->idmsaConnector = new IdmsaConnector($this);
        $this->appleAuthConnector = new AppleAuthConnector($this);
    }

    public function getAppleIdConnector(): AppleIdConnector
    {
        return $this->appleIdConnector;
    }

    public function getIdmsaConnector(): IdmsaConnector
    {
        return $this->idmsaConnector;
    }

    public function getAppleAuthConnector(): AppleAuthConnector
    {
        return $this->appleAuthConnector;
    }

    /**
     * @param string $account
     * @param string $password
     *
     * @throws \Saloon\Exceptions\Request\FatalRequestException
     * @throws \Saloon\Exceptions\Request\RequestException
     * @throws \JsonException
     *
     * @return Response\Response
     */
    public function authLogin(string $account, string $password): Response\Response
    {
        $initResponse = $this->getAppleAuthConnector()->appleAuthInit($account);

        $signinInitResponse = $this->getIdmsaConnector()->init(a: $initResponse->json('value'), account: $account);

        $completeResponse = $this->getAppleAuthConnector()->appleAuthComplete(
            key: $initResponse->json('key'),
            salt: $signinInitResponse->json('salt'),
            b: $signinInitResponse->json('b'),
            c: $signinInitResponse->json('c'),
            password: $password,
            iteration: $signinInitResponse->json('iteration'),
            protocol: $signinInitResponse->json('protocol')
        );

        return $this->getIdmsaConnector()->complete(
            account: $account,
            m1: $completeResponse->json('M1'),
            m2: $completeResponse->json('M2'),
            c: $completeResponse->json('c'),
        );
    }
}
