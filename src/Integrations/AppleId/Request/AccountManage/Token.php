<?php

namespace Apple\Client\Integrations\AppleId\Request\AccountManage;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class Token extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/account/manage/gs/ws/token';
    }
}