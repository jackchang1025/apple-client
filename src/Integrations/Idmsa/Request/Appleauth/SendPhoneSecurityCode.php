<?php

namespace Apple\Client\Integrations\Idmsa\Request\Appleauth;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class SendPhoneSecurityCode extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PUT;

    public function __construct(protected int $id){}

    protected function defaultBody(): array
    {
        return[
            'phoneNumber' => [
                'id' => $this->id,
            ],
            'mode'        => 'sms',
        ];
    }

    public function resolveEndpoint(): string
    {
        return '/appleauth/auth/verify/phone';
    }
}