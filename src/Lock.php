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
interface Lock
{
    /**
     * Receive lock identifier.
     */
    public function id(): string;

    /**
     * Checks if the lock has already been released.
     *
     * @return bool True if the lock has already been released, otherwise false.
     */
    public function released(): bool;

    /**
     * Releases the lock. No-op if the lock has already been released.
     *
     * @return Promise<void>
     */
    public function release(): Promise;
}
