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
     * @psalm-param non-empty-string $id
     * @psalm-param callable(): mixed $code
     *
     * @return Promise<mixed|void>
     */
    public function withLock(string $id, callable $code): Promise;
}
