<?php

/**
 * This file is part of the Your-Project-Name package.
 *
 * (c) Your Name <your-email@example.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Apple\Client\Integrations\Idmsa\Request\AppleAuth;

use Apple\Client\Integrations\Request;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;

class AuthorizeComplete extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected string $account,
        protected string $m1,
        protected string $m2,
        protected string $c,
        protected bool $rememberMe = false,
    ) {
    }

    public function resolveEndpoint(): string
    {
        return '/appleauth/auth/signin/complete?isRememberMeEnabled=true';
    }

    public function defaultBody(): array
    {
        return [
            'accountName' => $this->account,
            'm1' => $this->m1,
            'm2' => $this->m2,
            'c' => $this->c,
            'rememberMe' => $this->rememberMe,
        ];
    }

    public function hasRequestFailed(Response $response): ?bool
    {
        return $response->status() !== 409;
    }
}
