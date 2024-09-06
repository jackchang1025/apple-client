<?php

namespace Apple\Client\Integrations\Idmsa\Request\Appleauth;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class SendTrustedDeviceSecurityCode extends Request
{
    protected Method $method = Method::PUT;

    public function resolveEndpoint(): string
    {
        return '/appleauth/auth/verify/trusteddevice/securitycode';
    }

    public function hasRequestFailed(Response $response): ?bool
    {
        if ($response->clientError() && $response->status() === 412) {
            return false;
        }
        return $response->serverError() || $response->clientError();
    }
}