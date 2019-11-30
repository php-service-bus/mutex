<?php

/**
 * PHP Mutex implementation.
 *
 * @author  Maksim Masiukevich <dev@async-php.com>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types = 1);

namespace ServiceBus\Mutex;

/**
 *
 */
interface MutexFactory
{
    /**
     * Create lock for specified operation.
     */
    public function create(string $id): Mutex;
}
