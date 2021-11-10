<?php

/**
 * PHP Mutex implementation.
 *
 * @author  Maksim Masiukevich <contacts@desperado.dev>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=0);

namespace ServiceBus\Mutex;

use Amp\Promise;

/**
 *
 */
interface MutexService
{
    /**
     * @param callable():Promise $code
     *
     * @return Promise<void>
     */
    public function withLock(string $id, callable $code): Promise;
}
