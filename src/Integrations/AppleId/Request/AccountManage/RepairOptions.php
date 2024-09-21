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

use Apple\Client\Integrations\Request;
use Saloon\Enums\Method;

class RepairOptions extends Request
{
    protected Method $method = Method::GET;

    public function __construct(protected string $xAppleWidgetKey)
    {
    }
    public function resolveEndpoint(): string
    {
        return '/account/manage/repair/options';
    }

    public function defaultHeaders(): array
    {
        return [
            'X-Apple-Widget-Key' => $this->xAppleWidgetKey,
        ];
    }
}
