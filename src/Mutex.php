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

use Amp\Promise;

/**
 *
 */
interface Mutex
{
    /**
     * Acquires a lock on the mutex.
     *
     * @return Promise<\ServiceBus\Mutex\Lock>
     *
     * @throws \ServiceBus\Mutex\Exceptions\SyncException An error occurs when attempting to obtain the lock
     */
    public function acquire(): Promise;
}
