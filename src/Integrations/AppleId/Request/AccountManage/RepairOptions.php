<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Weijiajia\Integrations\AppleId\Request\AccountManage;

use Weijiajia\Integrations\Request;
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
