<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Apple\Client\Integrations\Idmsa\Request\AppleAuth;

use Apple\Client\Integrations\Request;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Traits\Body\HasJsonBody;

class SigninInit extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(protected string $a, protected string $account)
    {
    }

    public function defaultBody(): array
    {
        return[
            'a' => $this->a,
            'accountName' => $this->account,
            'protocols' => ['s2k','s2k_fo'],
        ];
    }

    public function resolveEndpoint(): string
    {
        return '/appleauth/auth/signin/init';
    }
}
