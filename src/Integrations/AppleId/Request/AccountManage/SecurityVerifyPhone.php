<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Weijiajia\Integrations\AppleId\Request\AccountManage;

use Weijiajia\Integrations\Request;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Traits\Body\HasJsonBody;

class SecurityVerifyPhone extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(protected string $countryCode, protected string $phoneNumber, protected string $countryDialCode, protected bool $nonFTEU = true)
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
                    'countryCode' => $this->countryCode,
                    'number' => $this->phoneNumber,
                    'countryDialCode' => $this->countryDialCode,
                    'nonFTEU' => $this->nonFTEU,
                ],
                'mode' => 'sms',
            ],
        ];
    }
}
