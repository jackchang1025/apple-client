<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Apple\Client;

use Apple\Client\Integrations\AppleAuth\AppleAuthConnector;
use Apple\Client\Integrations\AppleAuth\Request\Complete;
use Apple\Client\Integrations\AppleAuth\Request\Init;
use InvalidArgumentException;

trait AppleAuth
{
    abstract public function getAppleAuthConnector(): AppleAuthConnector;

    /**
     * @param string $account
     *
     * @throws \Saloon\Exceptions\Request\FatalRequestException
     * @throws \Saloon\Exceptions\Request\RequestException|\JsonException
     *
     * @return \Saloon\Http\Response
     */
    public function appleAuthInit(string $account): \Saloon\Http\Response
    {
        $response = $this->getAppleAuthConnector()->send(new Init($account));

        if (empty($response->json('key'))) {
            throw new InvalidArgumentException("key IS EMPTY");
        }

        if (empty($response->json('value'))) {
            throw new InvalidArgumentException("value IS EMPTY");
        }

        return $response;
    }

    /**
     * @param string $key
     * @param string $salt
     * @param string $b
     * @param string $c
     * @param string $password
     * @param string $iteration
     * @param string $protocol
     *
     * @throws \Saloon\Exceptions\Request\FatalRequestException
     * @throws \Saloon\Exceptions\Request\RequestException|\JsonException
     *
     * @return \Saloon\Http\Response
     */
    public function appleAuthComplete(string $key, string $salt, string $b, string $c, string $password, string $iteration, string $protocol): \Saloon\Http\Response
    {
        $response = $this->getAppleAuthConnector()->send(new Complete(key: $key, b: $b, salt: $salt, c: $c, password: $password, iteration: $iteration, protocol: $protocol));

        if (empty($response->json('M1'))) {
            throw new InvalidArgumentException("M1 IS EMPTY");
        }

        if (empty($response->json('M2'))) {
            throw new InvalidArgumentException("M2 IS EMPTY");
        }

        if (empty($response->json('c'))) {
            throw new InvalidArgumentException("c IS EMPTY");
        }

        return $response;
    }
}
