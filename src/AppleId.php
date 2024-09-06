<?php

namespace Apple\Client;

use Apple\Client\Integrations\AppleId\AppleIdConnector;
use Apple\Client\Integrations\AppleId\Request\AccountManage\SecurityVerifyPhone;
use Apple\Client\Integrations\AppleId\Request\AccountManage\SecurityVerifyPhoneSecurityCode;
use Apple\Client\Integrations\AppleId\Request\AccountManage\Token;
use Apple\Client\Integrations\AppleId\Request\AuthenticatePassword;
use Apple\Client\Integrations\AppleId\Request\Bootstrap;
use Apple\Client\Integrations\Response;

trait AppleId
{
    abstract function getAppleIdConnector():AppleIdConnector;

    /**
     * @return Response
     * @throws \Saloon\Exceptions\Request\FatalRequestException
     * @throws \Saloon\Exceptions\Request\RequestException
     */
    public function bootstrap(): Response
    {
        return $this->getAppleIdConnector()->send(new Bootstrap());
    }

    /**
     * @param string $password
     * @return Response
     * @throws \Saloon\Exceptions\Request\FatalRequestException
     * @throws \Saloon\Exceptions\Request\RequestException
     */
    public function authenticatePassword(string $password): Response
    {
        return $this->getAppleIdConnector()->send(new AuthenticatePassword($password));
    }

    /**
     * @return Response
     * @throws \Saloon\Exceptions\Request\FatalRequestException
     * @throws \Saloon\Exceptions\Request\RequestException
     */
    public function token(): Response
    {
        return $this->getAppleIdConnector()->send(new Token());
    }

    public function securityVerifyPhone( string $countryCode,  string $phoneNumber,  int $countryDialCode,  bool $nonFTEU = true): Response
    {
        return $this->getAppleIdConnector()->send(new SecurityVerifyPhone($countryCode, $phoneNumber, $countryDialCode, $nonFTEU));
    }

    public function securityVerifyPhoneSecurityCode( int $id, string $phoneNumber, string $countryCode, string $countryDialCode, string $code): Response
    {
        return$this->getAppleIdConnector()->send(new SecurityVerifyPhoneSecurityCode($id, $phoneNumber, $countryCode, $countryDialCode, $code));
    }
}