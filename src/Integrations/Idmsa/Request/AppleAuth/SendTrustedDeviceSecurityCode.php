<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Weijiajia\Integrations\Idmsa\Request\AppleAuth;

use Weijiajia\Integrations\Request;
use Saloon\Enums\Method;
use Saloon\Http\Response;

class SendTrustedDeviceSecurityCode extends Request
{
    protected Method $method = Method::PUT;

    public function resolveEndpoint(): string
    {
        return '/appleauth/auth/verify/trusteddevice/securitycode';
    }

    public function hasRequestFailed(Response $response): ?bool
    {
        if ($response->clientError() && $response->status() === 412) {
            return false;
        }

        return $response->serverError() || $response->clientError();
    }
}
