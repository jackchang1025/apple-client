<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Apple\Client\Response;

use Apple\Client\DataConstruct\ServiceError;
use Illuminate\Support\Collection;

trait HasServiceError
{
    /**
     * Get all service errors.
     *
     * @throws \JsonException
     *
     * @return Collection<int,ServiceError>
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
     * @throws \JsonException
     *
     * @return ServiceError|null
     */
    public function getFirstServiceError(): ?ServiceError
    {
        return $this->getServiceErrors()->first();
    }

    /**
     * Get authentication service errors.
     *
     * @throws \JsonException
     *
     * @return Collection<int,ServiceError>
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
     * @throws \JsonException
     *
     * @return ServiceError|null
     */
    public function getFirstAuthServiceError(): ?ServiceError
    {
        return $this->getAuthServiceErrors()->first();
    }
}
