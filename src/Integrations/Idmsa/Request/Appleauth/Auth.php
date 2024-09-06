<?php

namespace Apple\Client\Integrations\Idmsa\Request\Appleauth;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class Auth extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/appleauth/auth';
    }

    public function defaultHeaders(): array
    {
        return [
            'Accept'                  => 'text/html',
            'Content-Type'            => 'application/json',
        ];
    }
}