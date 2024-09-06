<?php

namespace Apple\Client\Trait;

use Apple\Client\Integrations\Idmsa\Config;

trait HasAppleConfig
{
    protected ?Config $appleConfig = null;

    public function appleConfig(): Config
    {
        return $this->appleConfig ??= new Config();
    }
}