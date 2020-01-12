<?php

/**
 * PHP Mutex implementation.
 *
 * @author  Maksim Masiukevich <dev@async-php.com>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types = 1);

namespace ServiceBus\Mutex\InMemory;

use ServiceBus\Mutex\Mutex;
use ServiceBus\Mutex\MutexFactory;

/**
 * Create simple in memory mutex.
 */
final class InMemoryMutexFactory implements MutexFactory
{
    /**
     * {@inheritdoc}
     */
    public function create(string $id): Mutex
    {
        return new InMemoryMutex($id);
    }
}
