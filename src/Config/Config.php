<?php

/**
 * This file is part of the Your-Project-Name package.
 *
 * (c) Your Name <your-email@example.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Apple\Client\Config;

use Saloon\Repositories\ArrayStore;

class Config extends ArrayStore
{
    protected array $default = [
        'apiUrl' => 'https://appleid.apple.com',
        'serviceKeyUrl' => 'https://appstoreconnect.apple.com/olympus/v1/app/config?hostname=itunesconnect.apple.com',
        'serviceKey' => 'af1139274f266b22b68c2a3e7ad932cb3c0bbe854e13a79af78dcc73136882c3',
        'serviceUrl' => 'https://idmsa.apple.com/appleauth',
        'environment' => 'idms_prod',
        'locale' => 'en_US',
        'language' => 'en-us',
        'timeOutInterval' => 15,
        'moduleTimeOutInSeconds' => 60,
        'XAppleIDSessionId' => null,
        'pageFeatures' => [
            'shouldShowNewCreate' => false,
            'shouldShowRichAnimations' => true,
        ],
        'signoutUrls' => ['https://apps.apple.com/includes/commerce/logout'],
        'phoneInfo' => [],
        'verify' => false,
    ];

    /**
     * Create a new Config instance.
     *
     * @param array<string,mixed> $data
     */
    public function __construct(array $data = [])
    {
        /**
         * Merge the default configuration with the provided data.
         *
         * @var array<string,mixed> $mergedData
         */
        $mergedData = array_merge($this->default, $data);

        parent::__construct($mergedData);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return data_get($this->data, $key, $default);
    }

    /**
     * Get the locale.
     *
     * @return string
     */
    public function getLocale(): string
    {
        return $this->get('locale');
    }

    public function getLanguage(): string
    {
        return $this->get('language');
    }

    public static function fromArray(array $data): self
    {
        return new self($data);
    }

    public function setPhoneInfo(array $phoneInfo): void
    {
        $this->add('phoneInfo', $phoneInfo);
    }

    public function getPhoneInfo(): array
    {
        return $this->get('phoneInfo');
    }

    public function getApiUrl(): string
    {
        return $this->get('apiUrl');
    }

    public function getServiceKey(): string
    {
        return $this->get('serviceKey');
    }

    public function getServiceUrl(): string
    {
        return $this->get('serviceUrl');
    }

    public function getEnvironment(): string
    {
        return $this->get('environment');
    }

    public function getTimeOutInterval(): int
    {
        return $this->get('timeOutInterval');
    }

    /**
     * Get the module time out in seconds
     *
     * @return int
     */
    public function getModuleTimeOutInSeconds(): int
    {
        return $this->get('moduleTimeOutInSeconds');
    }

    public function getPageFeatures(): array
    {
        return $this->get('pageFeatures');
    }

    public function getSignoutUrls(): array
    {
        return $this->get('signoutUrls');
    }

    public function getXAppleIDSessionId(): ?string
    {
        return $this->get('XAppleIDSessionId');
    }

    public function setXAppleIDSessionId(?string $XAppleIDSessionId): void
    {
        $this->add('XAppleIDSessionId', $XAppleIDSessionId);
    }
}
