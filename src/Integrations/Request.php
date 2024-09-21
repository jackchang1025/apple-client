<?php

/**
 * This file is part of the Your-Project-Name package.
 *
 * (c) Your Name <your-email@example.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Apple\Client\Integrations;

use Apple\Client\Header\HasPersistentHeaders;
use Apple\Client\Proxy\HasProxy;
use Saloon\Http\Request as SaloonRequest;

abstract class Request extends SaloonRequest
{
    use HasProxy;
    use HasPersistentHeaders;

}
