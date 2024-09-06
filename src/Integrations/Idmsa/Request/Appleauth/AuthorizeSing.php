<?php

namespace Apple\Client\Integrations\Idmsa\Request\Appleauth;

use Apple\Client\Integrations\Idmsa\Request\Request;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;

class AuthorizeSing extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected string $accountName,
        protected string $password,
        protected bool $rememberMe = true,
    ){}

    public function resolveEndpoint(): string
    {
        return '/appleauth/auth/signin';
    }

    public function defaultQuery(): array
    {
        return [
            'isRememberMeEnabled' => $this->rememberMe,
        ];
    }

    public function defaultBody(): array
    {
        return [
            'accountName' => $this->accountName,
            'password'    => $this->password,
            'rememberMe'  => $this->rememberMe,
        ];
    }

    public function hasRequestFailed(Response $response): ?bool
    {
        return $response->status() !== 409;
    }

    public function defaultHeaders(): array
    {
        return [
            'X-Apple-OAuth-Redirect-URI'  => $this->appleConfig()->getApiUrl(),
            'X-Apple-OAuth-Client-Id'     => $this->appleConfig()->getServiceKey(),
            'X-Apple-OAuth-Client-Type'   => 'firstPartyAuth',
            'x-requested-with'            => 'XMLHttpRequest',
            'X-Apple-OAuth-Response-Mode' => 'web_message',
            'X-APPLE-HC'                  => '1:11:20240629164439:4e19d05de1614b4ea7746036705248f0::1979',
            // todo 动态数据
            'X-Apple-Domain-Id'           => '1',
        ];
    }
}