<?php

namespace Apple\Client\Response;

use Apple\Client\DataConstruct\ServiceError;
use Illuminate\Support\Collection;

trait HasServiceError
{
    /**
     * Get all service errors.
     *
     * @return Collection
     * @throws \JsonException
     */
    public function getServiceErrors(): Collection
    {
        $errors = $this->json('service_errors', [])
            ?: $this->json('validationErrors', [])
                ?: $this->json('serviceErrors', []);

        return collect($errors)
            ->map(fn (array $serviceError) => new ServiceError($serviceError));
    }

    /**
     * Get the first service error.
     *
     * @return ServiceError|null
     * @throws \JsonException
     */
    public function getFirstServiceError(): ?ServiceError
    {
        return $this->getServiceErrors()->first();
    }

    /**
     * Get authentication service errors.
     *
     * @return Collection
     * @throws \JsonException
     */
    public function getAuthServiceErrors(): Collection
    {
        $errors = data_get($this->authorizeSing(), 'direct.twoSV.phoneNumberVerification.serviceErrors', []);

        return collect($errors)
            ->map(fn (array $serviceError) => new ServiceError($serviceError));
    }

    /**
     * Get the first authentication service error.
     *
     * @return ServiceError|null
     * @throws \JsonException
     */
    public function getFirstAuthServiceError(): ?ServiceError
    {
        return $this->getAuthServiceErrors()->first();
    }
}