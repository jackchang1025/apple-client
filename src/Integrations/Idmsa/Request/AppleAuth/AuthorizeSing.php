<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Weijiajia\Integrations\Idmsa\Request\AppleAuth;

use Weijiajia\Integrations\Request;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;

class AuthorizeSing extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected string $accountName,
        protected string $password,
        protected bool $rememberMe = true,
    ) {
    }

    public function resolveEndpoint(): string
    {
        return '/appleauth/auth/signin';
    }

    public function defaultQuery(): array
    {
        return [
            'isRememberMeEnabled' => $this->rememberMe,
        ];
    }

    public function defaultBody(): array
    {
        return [
            'accountName' => $this->accountName,
            'password' => $this->password,
            'rememberMe' => $this->rememberMe,
        ];
    }

    public function hasRequestFailed(Response $response): ?bool
    {
        return $response->status() !== 409;
    }
}
