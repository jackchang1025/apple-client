<?php

namespace Apple\Client\Header;

use Apple\Client\Integrations\Response;
use Apple\Client\Repositories\AbstractPersistentStore;
use Apple\Client\Repositories\HasAbstractPersistentStore;
use Apple\Client\Repositories\Repositories;
use Saloon\Enums\PipeOrder;
use Saloon\Http\PendingRequest;

trait HasHeaderStore
{
    use HasAbstractPersistentStore;
    protected static ?AbstractPersistentStore $headerRepositories = null;

    public function persistentHeaders():array
    {
        return [];
    }

    public function hasHeaderStorePrefix(): string
    {
        return 'header';
    }

    public function hasHeaderStoreTtl():int
    {
        return 3600;
    }

    public function defaultHeaderRepositories(): array
    {
        return [];
    }

    public function getHeaderRepositories(): AbstractPersistentStore
    {
        return self::$headerRepositories ??= new Repositories(
            clientId: $this->getClientId(),
            cache: $this->getCache(),
            ttl: $this->hasHeaderStoreTtl(),
            prefix: $this->hasHeaderStorePrefix(),
            defaultData: $this->defaultHeaderRepositories(),
        );
    }


    public function bootHasHeaderStore(PendingRequest $pendingRequest): void
    {
        $pendingRequest->middleware()
            ->onRequest(function (PendingRequest $pendingRequest){

                $persistentHeaders = [];
                $connector = $pendingRequest->getConnector();
                $request = $pendingRequest->getRequest();

                if (method_exists($connector,'persistentHeaders')){
                    $persistentHeaders = array_merge($connector->persistentHeaders(), $persistentHeaders);
                }

                if (method_exists($request,'persistentHeaders')){
                    $persistentHeaders = array_merge($request->persistentHeaders(), $persistentHeaders);
                }

                if (empty($persistentHeaders)){
                    return $pendingRequest;
                }

                $storedHeaders = $this->getHeaderRepositories()->all();
                foreach ($persistentHeaders as $header) {
                    if (!empty($storedHeaders[$header])) {
                        $pendingRequest->headers()->add($header, $storedHeaders[$header]);
                    }
                }

                return $pendingRequest;
            },'header_store_request',PipeOrder::LAST);

        $pendingRequest->middleware()
            ->onResponse(function (Response $response){
                $this->getHeaderRepositories()->merge($response->headers()->all());
            },'header_store_response',PipeOrder::LAST);
    }
}