<?php

/**
 * This file is part of the Your-Project-Name package.
 *
 * (c) Your Name <your-email@example.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Apple\Client\Response;

class Response extends \Saloon\Http\Response
{
    use HasServiceError;
    use HasAuth;
    use HasPhoneNumbers;
}
