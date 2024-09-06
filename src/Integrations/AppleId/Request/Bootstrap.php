<?php

namespace Apple\Client\Integrations\AppleId\Request;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class Bootstrap extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/bootstrap/portal';
    }
}