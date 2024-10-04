<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Apple\Client\Config;

use Saloon\Repositories\ArrayStore;

/**
 * Config class to store and manage application configuration settings.
 * Extends the base ArrayStore class to handle configuration as an array.
 */
class Config extends ArrayStore
{
    /**
     * Array containing default configuration settings for interacting with Apple's services.
     *
     * @var array<string,mixed>
     *
     * Key Descriptions:
     * - apiUrl: The base URL for Apple ID services.
     * - serviceKeyUrl: The URL to fetch the service key required for authentication.
     * - serviceKey: The pre-shared secret key used for authentication.
     * - serviceUrl: The URL for the Apple ID management service.
     * - environment: The target environment for the API requests (e.g., production, sandbox).
     * - locale: The default locale for the user interface.
     * - language: The default language for content, formatted as ISO 639-1 code.
     * - timeOutInterval: Default timeout interval in seconds for API requests.
     * - moduleTimeOutInSeconds: Timeout duration in seconds for individual module operations.
     * - XAppleIDSessionId: Session identifier for maintaining state across requests, if applicable.
     * - pageFeatures: Configuration flags for UI features, including new account creation and animations.
     * - signoutUrls: URLs to trigger logout from Apple services.
     * - phoneInfo: Array holding phone-specific information, if needed.
     * - verify: Boolean flag to enable or disable additional verification steps.
     */
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
     * Constructor for the class.
     *
     * Initializes the object by merging the default configuration with the provided data.
     *
     * @param array<int|string,mixed> $data Optional data to merge with the default configuration.
     *                                      Defaults to an empty array if not provided.
     *
     * @return void
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

    /**
     * Retrieves the value associated with the given key from the data store.
     *
     * If the key is not found in the data store, the default value is returned.
     *
     * @param string $key     The key whose value is to be retrieved.
     * @param mixed  $default The default value to return if the key is not found. Defaults to null.
     *
     * @return mixed The value associated with the key, or the default value if the key is not found.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return data_get($this->data, $key, $default);
    }

    /**
     * Retrieves the current locale setting.
     *
     * @return string The locale value associated with the key 'locale'.
     */
    public function getLocale(): string
    {
        return $this->get('locale');
    }

    /**
     * Retrieves the language setting.
     *
     * @return string The language code for the current context.
     */
    public function getLanguage(): string
    {
        return $this->get('language');
    }

    /**
     * Creates a new instance of the class from an associative array.
     *
     * @param array<int|string,mixed> $data The associative array containing the data to initialize the object.
     *
     * @return self A new instance of the class initialized with the provided array data.
     */
    public static function fromArray(array $data): self
    {
        return new self($data);
    }

    /**
     * Sets the phone information in the class instance.
     *
     * @param array<int|string,mixed> $phoneInfo An associative array containing phone information details.
     *
     * @return void
     */
    public function setPhoneInfo(array $phoneInfo): void
    {
        $this->add('phoneInfo', $phoneInfo);
    }

    /**
     * Retrieves the phone information.
     *
     * @return array<int|string,mixed> An associative array containing phone details.
     */
    public function getPhoneInfo(): array
    {
        return $this->get('phoneInfo');
    }

    /**
     * Retrieves the API URL from the configuration.
     *
     * @return string The API URL.
     */
    public function getApiUrl(): string
    {
        return $this->get('apiUrl');
    }

    /**
     * Retrieves the service key associated with this instance.
     *
     * @return string The service key.
     */
    public function getServiceKey(): string
    {
        return $this->get('serviceKey');
    }

    /**
     *
     */
    public function getServiceUrl(): string
    {
        return $this->get('serviceUrl');
    }

    /**
     * Retrieves the current environment setting.
     *
     * @return string The environment name.
     */
    public function getEnvironment(): string
    {
        return $this->get('environment');
    }

    /**
     *
     */
    public function getTimeOutInterval(): int
    {
        return $this->get('timeOutInterval');
    }

    /**
     * Retrieves the timeout duration in seconds for a module.
     *
     * @return int The timeout value in seconds.
     */
    public function getModuleTimeOutInSeconds(): int
    {
        return $this->get('moduleTimeOutInSeconds');
    }

    /**
     * Retrieves the page features.
     *
     * @return array<int|string,mixed> An array containing the features of the page.
     */
    public function getPageFeatures(): array
    {
        return $this->get('pageFeatures');
    }

    /**
     * Retrieves the signout URLs array.
     *
     * @return array<int|string,mixed> An array containing the signout URLs.
     */
    public function getSignoutUrls(): array
    {
        return $this->get('signoutUrls');
    }

    public function getXAppleIDSessionId(): ?string
    {
        return $this->get('XAppleIDSessionId');
    }

    /**
     * Sets the X-Apple-ID-Session-Id for the request.
     *
     * @param ?string $XAppleIDSessionId The X-Apple-ID-Session-Id value to be set. Can be null to remove the header.
     *
     * @return void
     */
    public function setXAppleIDSessionId(?string $XAppleIDSessionId): void
    {
        $this->add('XAppleIDSessionId', $XAppleIDSessionId);
    }
}
