<?php

namespace Apple\Client\Repositories;

use Saloon\Contracts\ArrayStore as ArrayStoreContract;

trait HasRepositories
{
    use HasAbstractPersistentStore;

    protected static ?ArrayStoreContract $repositories = null;

    public function prefix(): string
    {
        return 'repositories';
    }

    public function hasRepositoriesTtl():int
    {
        return 3600;
    }

    public function getRepositories(): ?ArrayStoreContract
    {
        return self::$repositories ??= new Repositories(
            clientId: $this->getClientId(),
            cache: $this->getCache(),
            ttl: $this->hasRepositoriesTtl(),
            prefix: $this->prefix(),
            defaultData: $this->defaultRepositories(),
        );
    }

    public function defaultRepositories(): array
    {
        return [];
    }
}