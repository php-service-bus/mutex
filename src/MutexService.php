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

interface MutexService
{
    /**
     * @template T as Promise|\Generator|mixed
     *
     * @psalm-param non-empty-string $id
     * @psalm-param callable(): T $code
     *
     * @return Promise<mixed>
     */
    public function withLock(string $id, callable $code): Promise;
}
