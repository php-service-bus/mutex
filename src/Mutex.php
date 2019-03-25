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

use Amp\Promise;

/**
 *
 */
interface Mutex
{
    /**
     * Acquires a lock on the mutex.
     *
     * @throws \ServiceBus\Mutex\Exceptions\SyncException An error occurs when attempting to obtain the lock
     *
     * @return \Amp\Promise<\ServiceBus\Mutex\Lock>
     */
    public function acquire(): Promise;
}