<?php

namespace Apple\Client\Integrations\Idmsa\Response;

use Apple\Client\Trait\Response\HasServiceError;
use Saloon\Http\Response;

class VerifyTrustedDeviceSecurityCodeResponse extends Response
{
    use HasServiceError;
}