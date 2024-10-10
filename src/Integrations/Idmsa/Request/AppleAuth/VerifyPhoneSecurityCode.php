<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Weijiajia\Integrations\Idmsa\Request\AppleAuth;

use Weijiajia\Integrations\Request;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Traits\Body\HasJsonBody;

class VerifyPhoneSecurityCode extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected string $id,
        protected string $code
    ) {
    }

    public function resolveEndpoint(): string
    {
        return '/appleauth/auth/verify/phone/securitycode';
    }

    public function defaultBody(): array
    {
        return[
            'phoneNumber' => [
                'id' => $this->id,
            ],
            'securityCode' => [
                'code' => $this->code,
            ],
            'mode' => 'sms',
        ];
    }
}
