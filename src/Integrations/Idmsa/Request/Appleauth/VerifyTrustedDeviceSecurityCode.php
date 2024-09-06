<?php

namespace Apple\Client\Integrations\Idmsa\Request\Appleauth;

use Apple\Client\Integrations\Idmsa\Response\AuthResponse;
use Apple\Client\Integrations\Idmsa\Response\VerifyTrustedDeviceSecurityCodeResponse;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;

class VerifyTrustedDeviceSecurityCode extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(protected string $code)
    {
    }

    public function resolveEndpoint(): string
    {
        return '/appleauth/auth/verify/trusteddevice/securitycode';
    }

    public function defaultBody(): array
    {
        return [
            'securityCode' => [
                'code' => $this->code,
            ],
        ];
    }

    public function defaultHeaders (): array
    {
        return [
        ];
    }

    public function hasRequestFailed(Response $response):bool
    {
        return $response->status() !== 412 && $response->status() !== 400 && $response->failed();
    }

}