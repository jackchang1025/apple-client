<?php

/**
 * This file is part of the Your-Project-Name package.
 *
 * (c) Your Name <your-email@example.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Apple\Client;

use Apple\Client\Config\HasConfig;
use Apple\Client\Cookies\HasCookie;
use Apple\Client\Header\HasHeaderSynchronize;
use Apple\Client\Helpers\Helpers;
use Apple\Client\Integrations\AppleAuth\AppleAuthConnector;
use Apple\Client\Integrations\AppleId\AppleIdConnector;
use Apple\Client\Integrations\Idmsa\IdmsaConnector;
use Apple\Client\Logger\Logger;
use Apple\Client\Proxy\HasProxy;
use GuzzleHttp\Cookie\CookieJarInterface;
use Psr\Log\LoggerInterface;
use Saloon\Contracts\ArrayStore as ArrayStoreContract;
use Saloon\Traits\Macroable;

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

    protected AppleIdConnector $appleIdConnector;
    protected IdmsaConnector $idmsaConnector;
    protected AppleAuthConnector $appleAuthConnector;

    /**
     * @param ArrayStoreContract                  $config
     * @param ArrayStoreContract|null         $headerRepositories
     * @param CookieJarInterface|null $cookieJar
     * @param LoggerInterface|null    $logger
     */
    public function __construct(
        ArrayStoreContract $config,
        ?ArrayStoreContract $headerRepositories = null,
        ?CookieJarInterface $cookieJar = null,
        ?LoggerInterface $logger = null,
    ) {

        $this->withConfig($config);
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
     * @return \Apple\Client\Response\Response
     *@throws \Saloon\Exceptions\Request\FatalRequestException
     * @throws \Saloon\Exceptions\Request\RequestException
     *
     * @throws \JsonException
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
