<?php

namespace Apple\Client\Integrations\Idmsa\Response;

use Apple\Client\Trait\Response\HasAuth;
use Apple\Client\Trait\Response\HasPhoneNumbers;
use Saloon\Http\Response;

class AuthResponse extends Response
{
    use HasAuth;
    use HasPhoneNumbers;

}