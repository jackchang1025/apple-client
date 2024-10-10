<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Weijiajia\Header;

use Saloon\Contracts\ArrayStore;
use Saloon\Enums\PipeOrder;
use Saloon\Http\PendingRequest;
use Saloon\Http\Response;

trait HasHeaderSynchronize
{
    protected ?ArrayStore $headerRepositories = null;

    public function setHeaderRepositories(?ArrayStore $headerRepositories): void
    {
        $this->headerRepositories = $headerRepositories;
    }

    public function getHeaderRepositories(): ?ArrayStore
    {
        return $this->headerRepositories;
    }

    public function bootHasHeaderSynchronize(PendingRequest $pendingRequest): void
    {
        if (!$this->getHeaderRepositories()) {
            return;
        }

        $pendingRequest->middleware()
            ->onRequest(function (PendingRequest $pendingRequest) {
                $persistentHeaders = [];
                $connector = $pendingRequest->getConnector();
                $request = $pendingRequest->getRequest();

                if (method_exists($connector, 'getPersistentHeaders')) {
                    $persistentHeaders = array_merge($persistentHeaders, $connector->getPersistentHeaders()?->all());
                }

                if (method_exists($request, 'getPersistentHeaders')) {
                    $persistentHeaders = array_merge($persistentHeaders, $request->getPersistentHeaders()?->all());
                }

                if (empty($persistentHeaders)) {
                    return $pendingRequest;
                }

                $storedHeaders = $this->getHeaderRepositories()?->all();

                foreach ($persistentHeaders as $key => $value) {
                    if (is_int($key)) {
                        $header = $value;
                        $defaultValue = null;
                    } else {
                        $header = $key;
                        $defaultValue = $value;
                    }

                    $headerValue = $storedHeaders[$header] ?? null;

                    if ($headerValue === null && $defaultValue !== null) {
                        $headerValue = is_callable($defaultValue) ? $defaultValue() : $defaultValue;
                    }

                    if ($headerValue !== null) {
                        $pendingRequest->headers()->add($header, $headerValue);
                    }
                }

                return $pendingRequest;
            }, 'header_store_request', PipeOrder::LAST);

        $pendingRequest->middleware()
            ->onResponse(function (Response $response) {
                $this->getHeaderRepositories()?->merge($response->headers()->all());

                return $response;
            }, 'header_store_response', PipeOrder::LAST);
    }
}
