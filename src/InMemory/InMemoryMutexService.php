<?php

/**
 * PHP Mutex implementation.
 *
 * @author  Maksim Masiukevich <contacts@desperado.dev>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=0);

namespace ServiceBus\Mutex\InMemory;

use Amp\Promise;
use ServiceBus\Mutex\MutexService;
use function Amp\call;
use function Amp\delay;

/**
 * Can only be used when working in one process.
 */
final class InMemoryMutexService implements MutexService
{
    private const LATENCY_TIMEOUT = 50;

    public function withLock(string $id, callable $code): Promise
    {
        return call(
            static function () use ($id, $code): \Generator
            {
                try
                {
                    while (InMemoryMutexStorage::instance()->has($id))
                    {
                        yield delay(self::LATENCY_TIMEOUT);
                    }

                    InMemoryMutexStorage::instance()->lock($id);

                    yield $code();
                }
                finally
                {
                    InMemoryMutexStorage::instance()->unlock($id);
                }
            }
        );
    }
}
