<?php

namespace Apple\Client\Logger;

use Apple\Client\Integrations\AppleConnector;
use GuzzleHttp\Middleware;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Saloon\Http\PendingRequest;
use Saloon\Http\Senders\GuzzleSender;

trait Logger
{
    abstract public function getLogger(): LoggerInterface;

    public function bootLogger(PendingRequest $pendingRequest): void
    {
        /**
         * @var AppleConnector $connector
         */
        $connector = $pendingRequest->getConnector();

        $sender = $connector->sender();
        if ($sender instanceof GuzzleSender){

            $repositories = $connector->getRepositories();
            $sender->getHandlerStack()
                ->push(Middleware::mapRequest(function (RequestInterface $request) use ($repositories) {

                    $this->getLogger()->info('apple-client Request', [
                        'account' => $repositories->get('account'),
                        'method'  => $request->getMethod(),
                        'uri'     => (string) $request->getUri(),
                        'headers' => $request->getHeaders(),
                        'body'    => (string)$request->getBody(),
                    ]);

                    return $request;
                }));

            $sender->getHandlerStack()
                ->push(Middleware::mapResponse(function (ResponseInterface $response) use ($repositories){

                    $contentType = $response->getHeaderLine('Content-Type');

                    if ($contentType !== 'text/html;charset=UTF-8') {
                        $this->getLogger()->info('apple-client Response', [
                            'account' => $repositories->get('account'),
                            'status'  => $response->getStatusCode(),
                            'headers' => $response->getHeaders(),
                            'body'    => (string) $response->getBody(),
                        ]);
                    }
                    return $response;
                }));
        }
    }
}