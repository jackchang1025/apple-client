<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Weijiajia\Integrations\AppleId\Request\AccountManage;

use Weijiajia\Integrations\Request;
use Saloon\Enums\Method;

class PrivacyAccept extends Request
{
    protected Method $method = Method::OPTIONS;

    public function __construct(protected string $xAppleWidgetKey)
    {
    }

    public function resolveEndpoint(): string
    {
        return '/account/manage/privacy/accept';
    }

    public function defaultHeaders(): array
    {
        return [
            'X-Apple-Widget-Key' => $this->xAppleWidgetKey,
        ];
    }

    public function persistentHeaders(): array
    {
        return ['X-Apple-ID-Session-Id','X-Apple-OAuth-Context','X-Apple-Session-Token'];
    }
}
