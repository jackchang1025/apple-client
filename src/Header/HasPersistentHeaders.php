<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Apple\Client\Header;

use Saloon\Contracts\ArrayStore as ArrayStoreContract;
use Saloon\Repositories\ArrayStore;

/**
 * Trait HasPersistentHeaders provides methods to manage and handle persistent headers within a class.
 *
 * This trait introduces the ability to set and retrieve persistent header data using an ArrayStoreContract implementation.
 * It offers a flexible way to associate and carry header information across requests or processes by integrating
 * with classes that use this trait.
 * **Usage:**
 * - Use `withPersistentHeaders` to set custom persistent headers.
 * - Access stored headers through `getPersistentHeaders`.
 * - Override `defaultPersistentHeaders` to define default header values when none are set.
 */
trait HasPersistentHeaders
{
    /**
     * Represents a collection of headers that should persist across multiple requests.
     * This variable holds the value of headers which are intended to be sent with every HTTP request.
     * Initially set to null, it can be assigned an array where each key-value pair represents a header name and its value.
     */
    protected ?ArrayStoreContract $persistentHeaders = null;

    /**
     * Sets the persistent headers to be used and returns the current instance.
     *
     * @param ArrayStoreContract $persistentHeaders The contract storing persistent headers to be applied.
     *
     * @return static The current instance with updated persistent headers.
     */
    public function withPersistentHeaders(ArrayStoreContract $persistentHeaders): static
    {
        $this->persistentHeaders = $persistentHeaders;

        return $this;
    }

    /**
     * Retrieves the persistent headers store.
     *
     * @return ArrayStoreContract The store containing persistent headers, defaulting to an instance initialized with default headers if not set.
     */
    public function getPersistentHeaders(): ArrayStoreContract
    {
        return $this->persistentHeaders ?? new ArrayStore($this->defaultPersistentHeaders());
    }

    /**
     * Retrieves the default set of persistent headers.
     *
     * @return array<string|int,mixed> An array containing the default persistent headers.
     */
    public function defaultPersistentHeaders(): array
    {
        return [];
    }
}
