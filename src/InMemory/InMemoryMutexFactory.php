<?php

/**
 * PHP Mutex implementation.
 *
 * @author  Maksim Masiukevich <contacts@desperado.dev>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types = 0);

namespace ServiceBus\Mutex\InMemory;

use ServiceBus\Mutex\Mutex;
use ServiceBus\Mutex\MutexFactory;

/**
 * Create simple in memory mutex.
 */
final class InMemoryMutexFactory implements MutexFactory
{
    public function create(string $id): Mutex
    {
        return new InMemoryMutex($id);
    }
}
