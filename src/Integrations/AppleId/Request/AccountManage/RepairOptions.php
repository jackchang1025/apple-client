<?php

namespace Apple\Client\Integrations\AppleId\Request\AccountManage;

use Apple\Client\Integrations\Idmsa\Request\Request;
use Saloon\Enums\Method;

class RepairOptions extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/account/manage/repair/options';
    }

    public function defaultHeaders(): array
    {
        return [
            'X-Apple-Widget-Key'    => $this->appleConfig()->getServiceKey(),
        ];
    }
}