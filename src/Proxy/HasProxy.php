<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Weijiajia\Proxy;

use Weijiajia\Config\HasConfig;
use GuzzleHttp\RequestOptions;
use InvalidArgumentException;
use Saloon\Http\PendingRequest;

trait HasProxy
{
    use HasConfig;

    protected ?string $proxy = null;

    protected bool $proxyEnabled = true;

    public function isProxyEnabled(): bool
    {
        return $this->proxyEnabled;
    }

    public function setProxyEnabled(bool $proxyEnabled): void
    {
        $this->proxyEnabled = $proxyEnabled;
    }

    public function bootHasProxy(PendingRequest $pendingRequest): void
    {
        if ($this->getProxy() && $this->isProxyEnabled()) {

            $pendingRequest->config()
                ->add(RequestOptions::PROXY, $this->getProxy());
        }
    }

    public function getProxy(): ?string
    {
        return $this->proxy;
    }

    public function withProxy(?string $proxy = null): static
    {
        if ($proxy !== null && !$this->isValidProxyUrl($proxy)) {
            throw new InvalidArgumentException("Invalid proxy URL: $proxy");
        }

        $this->proxy = $proxy;

        return $this;
    }

    protected function isValidProxyUrl(string $url): bool
    {
        // Regular expression pattern to match a valid URL
        // Updated regular expression pattern to match valid URLs including IP addresses
        $pattern = '/^(http|https):\/\/'
            . '('
            . '(([a-zA-Z0-9]|[a-zA-Z0-9][a-zA-Z0-9\-]*[a-zA-Z0-9])\.)*([A-Za-z0-9]|[A-Za-z0-9][A-Za-z0-9\-]*[A-Za-z0-9])'
            . '|'
            . '((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)'
            . '|'
            . '\[[0-9a-fA-F:]+\]' // IPv6
            . '|'
            . 'localhost'
            . ')'
            . '(:[0-9]{1,5})?'
            . '(\/.*)?$/i';

        return (bool) preg_match($pattern, $url);
    }
}
