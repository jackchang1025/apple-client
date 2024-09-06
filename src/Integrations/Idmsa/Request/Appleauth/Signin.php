<?php

namespace Apple\Client\Integrations\Idmsa\Request\Appleauth;

use Apple\Client\Integrations\Idmsa\Request\Request;
use Saloon\Enums\Method;

class Signin extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/appleauth/auth/authorize/signin';
    }

    public function defaultQuery(): array{

        return [
            'frame_id'      => $this->buildUUid(),
            'skVersion'     => '7',
            'iframeId'      => $this->buildUUid(),
            'client_id'     => $this->appleConfig()->getServiceKey(),
            'redirect_uri'  => $this->appleConfig()->getApiUrl(),
            'response_type' => 'code',
            'response_mode' => 'web_message',
            'state'         => $this->buildUUid(),
            'authVersion'   => 'latest',
        ];
    }

    public function defaultHeaders(): array
    {
        return [
            'Sec-Fetch-Site' => 'same-origin',
            'Sec-Fetch-Mode' => 'navigate',
            'Sec-Fetch-Dest' => 'iframe',
        ];
    }
}