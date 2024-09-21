<?php

/**
 * This file is part of the Your-Project-Name package.
 *
 * (c) Your Name <your-email@example.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Apple\Client\Logger;

use GuzzleHttp\Middleware;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Saloon\Http\PendingRequest;
use Saloon\Http\Senders\GuzzleSender;

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
        return function (RequestInterface $request) {
            $this->getLogger()
                ->debug('request', [
                    'method' => $request->getMethod(),
                    'uri' => (string) $request->getUri(),
                    'headers' => $request->getHeaders(),
                    'body' => (string) $request->getBody(),
                ]);

            return $request;
        };
    }

    public function defaultResponseMiddle(PendingRequest $pendingRequest): \Closure
    {
        return function (ResponseInterface $response) {
            $this->getLogger()
                ->info('response', [
                    'status' => $response->getStatusCode(),
                    'headers' => $response->getHeaders(),
                    'body' => (string) $response->getBody(),
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

        $connector = $pendingRequest->getConnector();

        $sender = $connector->sender();

        if ($sender instanceof GuzzleSender) {
            $sender->getHandlerStack()
                ->push(Middleware::mapRequest($this->defaultRequestMiddle($pendingRequest)));

            $sender->getHandlerStack()
                ->push(Middleware::mapResponse($this->defaultResponseMiddle($pendingRequest)));
        }
    }
}
