<?php

/**
 * This file is part of the Your-Project-Name package.
 *
 * (c) Your Name <your-email@example.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Apple\Client\Integrations\AppleId\Request\AccountManage;

use Apple\Client\Exception\AccountLockoutException;
use Apple\Client\Exception\BindPhoneException;
use Apple\Client\Exception\ErrorException;
use Apple\Client\Exception\PhoneException;
use Apple\Client\Exception\PhoneNumberAlreadyExistsException;
use Apple\Client\Exception\VerificationCodeSentTooManyTimesException;
use Apple\Client\Integrations\Request;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;
use Throwable;

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
