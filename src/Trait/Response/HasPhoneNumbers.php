<?php

namespace Apple\Client\Trait\Response;

use Apple\Client\DataConstruct\Phone;
use Apple\Client\DataConstruct\ServiceError;
use Illuminate\Support\Collection;

trait HasPhoneNumbers
{
    public function getTrustedPhoneNumber(): ?Phone
    {
        $data = $this->authorizeSing()['direct']['twoSV']['phoneNumberVerification']['trustedPhoneNumber'] ?? [];

        return $data ? new Phone($data) : null;
    }

    /**
     * 获取所有信任的电话号码
     *
     * @return Collection
     */
    public function getTrustedPhoneNumbers(): Collection
    {
        return collect($this->authorizeSing()['direct']['twoSV']['phoneNumberVerification']['trustedPhoneNumbers'] ?? [])
            ->map(fn(array $phone) => new Phone($phone));
    }
}