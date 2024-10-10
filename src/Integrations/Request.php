<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Weijiajia\Integrations;

use Weijiajia\Header\HasPersistentHeaders;
use Weijiajia\Proxy\HasProxy;
use Saloon\Http\Request as SaloonRequest;

abstract class Request extends SaloonRequest
{
    use HasProxy;
    use HasPersistentHeaders;
}
