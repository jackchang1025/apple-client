<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Apple\Client\Integrations\Idmsa\Request\AppleAuth;

use Apple\Client\Integrations\Request;
use Saloon\Enums\Method;

class AuthRepairComplete extends Request
{
    protected Method $method = Method::POST;

    public function resolveEndpoint(): string
    {
        return '/appleauth/auth/repair/complete';
    }

    public function defaultHeaders(): array
    {
        return [
//            'X-Apple-Repair-Session-Token' => $this->user->getHeader('X-Apple-Repair-Session-Token') ?? '',
        ];
    }
}
