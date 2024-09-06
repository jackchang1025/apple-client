<?php

namespace Apple\Client\Integrations;

use Apple\Client\Trait\Response\HasAuth;
use Apple\Client\Trait\Response\HasPhoneNumbers;
use Apple\Client\Trait\Response\HasServiceError;

class Response extends \Saloon\Http\Response
{
    use HasServiceError;
    use HasAuth;
    use HasPhoneNumbers;
}