<?php

namespace Apple\Client;

use Apple\Client\Exception\VerificationCodeException;
use Apple\Client\Integrations\Idmsa\IdmsaConnector;
use Apple\Client\Integrations\Idmsa\Request\Appleauth\Auth;
use Apple\Client\Integrations\Idmsa\Request\Appleauth\AuthorizeSing;
use Apple\Client\Integrations\Idmsa\Request\Appleauth\AuthRepairComplete;
use Apple\Client\Integrations\Idmsa\Request\Appleauth\SendPhoneSecurityCode;
use Apple\Client\Integrations\Idmsa\Request\Appleauth\SendTrustedDeviceSecurityCode;
use Apple\Client\Integrations\Idmsa\Request\Appleauth\Signin;
use Apple\Client\Integrations\Idmsa\Request\Appleauth\VerifyPhoneSecurityCode;
use Apple\Client\Integrations\Idmsa\Request\Appleauth\VerifyTrustedDeviceSecurityCode;
use Apple\Client\Integrations\Response;

trait Idmsa
{
    abstract function getIdmsaConnector():IdmsaConnector;

    public function sign(): Response
    {
        return $this->getIdmsaConnector()->send(new Signin());
    }

    public function authorizeSing( string $accountName, string $password, bool $rememberMe = true,): Response
    {
        return $this->getIdmsaConnector()->send(new AuthorizeSing($accountName, $password, $rememberMe));
    }

    public function auth(): Response
    {
        return $this->getIdmsaConnector()->send(new Auth());
    }

    public function verifySecurityCode(string $code): Response
    {
        $response = $this->getIdmsaConnector()->send(new VerifyTrustedDeviceSecurityCode($code));

        if ($response->status() === 412){

            $this->managePrivacyAccept();
        }else if ($response->status() === 400) {

            throw new VerificationCodeException($response->service_errors_first()?->getMessage(), $response->status());
        }

        return $response;
    }

    public function verifyPhoneCode(string $id,string $code): Response
    {
        $response = $this->getIdmsaConnector()->send(new VerifyPhoneSecurityCode($id,$code));

        if ($response->status() === 412){

            $this->managePrivacyAccept();

        }else if ($response->status() === 400) {

            throw new VerificationCodeException($response->service_errors_first()?->getMessage(), $response->status());
        }

        return $response;
    }

    public function sendSecurityCode(): Response
    {
        return $this->getIdmsaConnector()->send(new SendTrustedDeviceSecurityCode());
    }

    public function sendPhoneSecurityCode(int $id): Response
    {
        return $this->getIdmsaConnector()->send(new SendPhoneSecurityCode($id));
    }

    public function managePrivacyAccept(): Response
    {
        return $this->idmsaConnector->send(new AuthRepairComplete());
    }
}