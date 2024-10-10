<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Weijiajia\Integrations\AppleAuth\Request;

use Weijiajia\Integrations\Request;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Traits\Body\HasJsonBody;

class Init extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    protected bool $proxyEnabled = false;

    public function __construct(protected string $account)
    {
    }

    public function defaultBody(): array
    {
        return[
            'email' => $this->account,
        ];
    }

    public function resolveEndpoint(): string
    {
        return '/init';
    }

    public function defaultHeaders(): array
    {
        return [
            'Accept' => 'text/html',
            'Content-Type' => 'application/json',
        ];
    }
}
