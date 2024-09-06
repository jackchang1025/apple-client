<?php

namespace Apple\Client\Integrations\AppleId\Request\AccountManage;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;

class SecurityVerifyPhone extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(protected string $countryCode, protected string $phoneNumber, protected int $countryDialCode, protected bool $nonFTEU = true)
    {
    }


    public function resolveEndpoint(): string
    {
        return '/account/manage/security/verify/phone';
    }

    protected function defaultBody(): array
    {
        return [
            'phoneNumberVerification' => [
                'phoneNumber' => [
                    'countryCode'     => $this->countryCode,
                    'number'          => $this->phoneNumber,
                    'countryDialCode' => $this->countryDialCode,
                    'nonFTEU'         => $this->nonFTEU,
                ],
                'mode'        => 'sms',
            ],
        ];
    }

    public function hasRequestFailed(Response $response): bool
    {
        if ($response->clientError() && $response->status() === 423){
            return false;
        }

        return $response->serverError() || $response->clientError();
    }
}