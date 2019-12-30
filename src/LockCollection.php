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
interface LockCollection
{
    public function has(string $key): Promise;

    public function place(string $key, Lock $lock): Promise;

    /**
     * Returns \ServiceBus\Mutex\Lock|null
     */
    public function extract(string $key): Promise;
}
