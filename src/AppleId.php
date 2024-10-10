<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Weijiajia;

use Weijiajia\Exception\AccountLockoutException;
use Weijiajia\Exception\BindPhoneException;
use Weijiajia\Exception\ErrorException;
use Weijiajia\Exception\PhoneException;
use Weijiajia\Exception\PhoneNumberAlreadyExistsException;
use Weijiajia\Exception\StolenDeviceProtectionException;
use Weijiajia\Exception\VerificationCodeSentTooManyTimesException;
use Weijiajia\Integrations\AppleId\AppleIdConnector;
use Weijiajia\Integrations\AppleId\Request\AccountManage\SecurityVerifyPhone;
use Weijiajia\Integrations\AppleId\Request\AccountManage\SecurityVerifyPhoneSecurityCode;
use Weijiajia\Integrations\AppleId\Request\AccountManage\Token;
use Weijiajia\Integrations\AppleId\Request\AuthenticatePassword;
use Weijiajia\Integrations\AppleId\Request\Bootstrap;
use Weijiajia\Response\Response;
use JsonException;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;

trait AppleId
{
    /**
     * @throws RequestException
     * @throws FatalRequestException
     *
     * @return Response
     */
    public function bootstrap(): Response
    {
        return $this->getAppleIdConnector()->send(new Bootstrap());
    }

    abstract public function getAppleIdConnector(): AppleIdConnector;

    /**
     * @param string $password
     *
     * @throws RequestException
     * @throws FatalRequestException
     *
     * @return Response
     */
    public function authenticatePassword(string $password): Response
    {
        return $this->getAppleIdConnector()
            ->send(new AuthenticatePassword($password));
    }

    /**
     * @throws RequestException
     * @throws FatalRequestException
     *
     * @return Response
     */
    public function token(): Response
    {
        return $this->getAppleIdConnector()->send(new Token());
    }

    /**
     * @param string $countryCode
     * @param string $phoneNumber
     * @param string $countryDialCode
     * @param bool   $nonFTEU
     *
     * @throws RequestException
     * @throws AccountLockoutException
     * @throws PhoneException
     * @throws VerificationCodeSentTooManyTimesException
     * @throws ErrorException
     * @throws PhoneNumberAlreadyExistsException
     * @throws BindPhoneException|JsonException
     *
     * @return Response
     */
    public function securityVerifyPhone(
        string $countryCode,
        string $phoneNumber,
        string $countryDialCode,
        bool $nonFTEU = true
    ): Response {
        try {
            return $this->getAppleIdConnector()
                ->send(new SecurityVerifyPhone($countryCode, $phoneNumber, $countryDialCode, $nonFTEU));
        } catch (RequestException $e) {
            /**
             * @var Response $response
             */
            $response = $e->getResponse();

            if ($response->successful() || $response->status() === 423) {
                return $response;
            }

            if ($response->status() === 467) {
                throw  new StolenDeviceProtectionException(response: $response);
            }

            $error = $response->getFirstServiceError();

            // 骏证码无法发送至该电话号码。请稍后重试
            if ($error?->getCode() === -28248) {
                throw new PhoneException(
                    response: $response
                );
            }

            //发送验证码的次数过多。输入你最后收到的验证码，或稍后再试。
            if ($error?->getCode() === -22979) {
                throw new VerificationCodeSentTooManyTimesException(
                    response: $response
                );
            }

            //Error Description not available
            if ($error?->getCode() === -22420) {
                throw new ErrorException(
                    response: $response
                );
            }

            if ($error?->getCode() === 'phone.number.already.exists') {
                throw new PhoneNumberAlreadyExistsException(
                    response: $response
                );
            }

            throw new BindPhoneException(
                response: $response
            );
        }
    }

    /**
     * @param int    $id
     * @param string $phoneNumber
     * @param string $countryCode
     * @param string $countryDialCode
     * @param string $code
     *
     * @throws RequestException
     * @throws FatalRequestException
     *
     * @return Response
     */
    public function securityVerifyPhoneSecurityCode(
        int $id,
        string $phoneNumber,
        string $countryCode,
        string $countryDialCode,
        string $code
    ): Response {
        return $this->getAppleIdConnector()
            ->send(new SecurityVerifyPhoneSecurityCode($id, $phoneNumber, $countryCode, $countryDialCode, $code));
    }
}
