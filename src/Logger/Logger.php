<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Apple\Client\Logger;

use Psr\Log\LoggerInterface;
use Saloon\Http\PendingRequest;
use Saloon\Http\Response;

trait Logger
{
    protected bool $booted = false;
    protected ?LoggerInterface $logger = null;

    public function setLogger(?LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    public function getLogger(): ?LoggerInterface
    {
        return $this->logger;
    }

    public function defaultRequestMiddle(PendingRequest $pendingRequest): \Closure
    {
        return function (PendingRequest $request) {
            $this->getLogger()?->debug('request', [
                    'method' => $request->getMethod(),
                    'uri' => (string) $request->getUri(),
                    'headers' => $request->headers(),
                    'body' => $request->body()?->all(),
                ]);

            return $request;
        };
    }

    public function defaultResponseMiddle(PendingRequest $pendingRequest): \Closure
    {
        return function (Response $response) {
            $this->getLogger()?->debug('response', [
                    'status' => $response->status(),
                    'headers' => $response->headers(),
                    'body' => $response->body(),
                ]);

            return $response;
        };
    }

    public function bootLogger(PendingRequest $pendingRequest): void
    {
        if ($this->booted || $this->getLogger() === null) {
            return;
        }

        $this->booted = true;

        $pendingRequest->getConnector()->middleware()->onRequest($this->defaultRequestMiddle($pendingRequest));
        $pendingRequest->getConnector()->middleware()->onResponse($this->defaultResponseMiddle($pendingRequest));
    }
}
