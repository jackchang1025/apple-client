<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Weijiajia\Integrations\Idmsa\Request\AppleAuth;

use Weijiajia\Integrations\Request;
use Saloon\Enums\Method;

class Auth extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/appleauth/auth';
    }

    public function defaultHeaders(): array
    {
        return [
            'Accept' => 'text/html',
            'Content-Type' => 'application/json',
        ];
    }
}
