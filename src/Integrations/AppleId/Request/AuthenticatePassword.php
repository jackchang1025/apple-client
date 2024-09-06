<?php

namespace Apple\Client\Integrations\AppleId\Request;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;

class AuthenticatePassword extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(protected string $password)
    {
    }


    public function resolveEndpoint(): string
    {
        return '/authenticate/password';
    }

    protected function defaultBody(): array
    {
        return [
            'password' => $this->password,
        ];
    }

    public function hasRequestFailed(Response $response): bool
    {
        if ($response->clientError() && $response->status() === 409){
            return false;
        }

        return $response->serverError() || $response->clientError();
    }
}