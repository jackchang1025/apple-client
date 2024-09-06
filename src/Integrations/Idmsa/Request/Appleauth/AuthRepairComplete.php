<?php

namespace Apple\Client\Integrations\Idmsa\Request\Appleauth;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class AuthRepairComplete extends Request
{
    protected Method $method = Method::POST;

    public function resolveEndpoint(): string
    {
        return '/appleauth/auth/repair/complete';
    }

    public function defaultHeaders(): array
    {
        return [
//            'X-Apple-Repair-Session-Token' => $this->user->getHeader('X-Apple-Repair-Session-Token') ?? '',
        ];
    }

}