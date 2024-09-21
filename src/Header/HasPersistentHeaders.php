<?php

namespace Apple\Client\Header;

use Saloon\Contracts\ArrayStore as ArrayStoreContract;
use Saloon\Repositories\ArrayStore;

trait HasPersistentHeaders
{
    protected ?ArrayStoreContract $persistentHeaders = null;

    public function withPersistentHeaders(ArrayStoreContract $persistentHeaders): static
    {
        $this->persistentHeaders = $persistentHeaders;
        return $this;
    }

    public function getPersistentHeaders(): ArrayStoreContract
    {
        return $this->persistentHeaders ?? new ArrayStore($this->defaultPersistentHeaders());
    }

    public function defaultPersistentHeaders():array
    {
        return [];
    }
}