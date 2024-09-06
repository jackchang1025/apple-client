<?php

namespace Apple\Client\Integrations\Idmsa;

class Config
{
    public function __construct(
        protected string $apiUrl = 'https://appleid.apple.com',
        protected string $serviceKey = 'af1139274f266b22b68c2a3e7ad932cb3c0bbe854e13a79af78dcc73136882c3',
        protected string $serviceUrl = 'https://idmsa.apple.com/appleauth',
        protected string $environment = 'idms_prod',
        protected int $timeOutInterval = 15,
        protected int $moduleTimeOutInSeconds = 60,
        protected ?string $XAppleIDSessionId = null,
        protected array $pageFeatures = [
            "shouldShowNewCreate"      => false,
            "shouldShowRichAnimations" => true,
        ],
        protected array $signoutUrls = ["https://apps.apple.com/includes/commerce/logout"],
        protected array $phoneInfo = []
    ) {

    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }

    public static function fromArray(array $data): self
    {
        return new self(...$data);
    }

    public function setPhoneInfo(array $phoneInfo): void
    {
        $this->phoneInfo = $phoneInfo;
    }

    public function getPhoneInfo(): array
    {
        return $this->phoneInfo;
    }


    public function getApiUrl(): string
    {
        return $this->apiUrl;
    }

    public function getServiceKey(): string
    {
        return $this->serviceKey;
    }

    public function getServiceUrl(): string
    {
        return $this->serviceUrl;
    }

    public function getEnvironment(): string
    {
        return $this->environment;
    }

    public function getTimeOutInterval(): int
    {
        return $this->timeOutInterval;
    }

    public function getModuleTimeOutInSeconds(): int
    {
        return $this->moduleTimeOutInSeconds;
    }

    public function getPageFeatures(): array
    {
        return $this->pageFeatures;
    }

    public function getSignoutUrls(): array
    {
        return $this->signoutUrls;
    }

    public function getXAppleIDSessionId(): string
    {
        return $this->XAppleIDSessionId;
    }

    public function setXAppleIDSessionId(string $XAppleIDSessionId): void
    {
        $this->XAppleIDSessionId = $XAppleIDSessionId;
    }
}
