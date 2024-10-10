<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Weijiajia\Integrations\Idmsa\Request\AppleAuth;

use Weijiajia\Integrations\Request;
use Saloon\Enums\Method;

class Signin extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        private readonly string $frameId,
        private readonly string $clientId,
        private readonly string $redirectUri,
        private readonly string $state
    ) {
    }

    public function resolveEndpoint(): string
    {
        return '/appleauth/auth/authorize/signin';
    }

    public function defaultQuery(): array
    {
        return [
            'frame_id' => $this->frameId,
            'skVersion' => '7',
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'response_type' => 'code',
            'response_mode' => 'web_message',
            'state' => $this->state,
            'authVersion' => 'latest',
        ];
    }

    public function defaultHeaders(): array
    {
        return [
            'Sec-Fetch-Site' => 'same-origin',
            'Sec-Fetch-Mode' => 'navigate',
            'Sec-Fetch-Dest' => 'iframe',
        ];
    }
}
