<?php

/**
 * This file is part of the Your-Project-Name package.
 *
 * (c) Your Name <your-email@example.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Apple\Client\Response;

use Apple\Client\DataConstruct\Phone;
use Illuminate\Support\Collection;

trait HasPhoneNumbers
{
    /**
     * @return Phone|null
     * @throws \JsonException
     */
    public function getTrustedPhoneNumber(): ?Phone
    {
        $data =  data_get($this->authorizeSing(), 'direct.twoSV.phoneNumberVerification.trustedPhoneNumber');

        return $data ? new Phone($data) : null;
    }

    /**
     * 获取所有信任的电话号码
     *
     * @return Collection
     * @throws \JsonException
     */
    public function getTrustedPhoneNumbers(): Collection
    {
        return collect(data_get($this->authorizeSing(), 'direct.twoSV.phoneNumberVerification.trustedPhoneNumbers',[]))
            ->map(fn (array $phone) => new Phone($phone));
    }

    /**
     * 获取电话号码验证信息
     * @return array|null
     * @throws \JsonException
     */
    public function phoneNumberVerification(): ?array
    {
        return $this->json('phoneNumberVerification');
    }
}
