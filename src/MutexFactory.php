<?php

/**
 * PHP Mutex implementation.
 *
 * @author  Maksim Masiukevich <contacts@desperado.dev>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types = 0);

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
