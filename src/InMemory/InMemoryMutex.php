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

use ServiceBus\Mutex\AmpLock;
use ServiceBus\Mutex\Mutex;
use function Amp\call;
use Amp\Promise;
use function Amp\delay;

/**
 * Can only be used when working in one process.
 *
 * @internal Created by factory (InMemoryMutexFactory::create())
 *
 * @see InMemoryMutexFactory
 */
final class InMemoryMutex implements Mutex
{
    private const LATENCY_TIMEOUT = 10;

    /**
     * @var string
     */
    private $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function __destruct()
    {
        InMemoryMutexStorage::instance()->unlock($this->id);
    }

    public function acquire(): Promise
    {
        return call(
            function (): \Generator
            {
                while (InMemoryMutexStorage::instance()->has($this->id))
                {
                    yield delay(self::LATENCY_TIMEOUT);
                }

                InMemoryMutexStorage::instance()->lock($this->id);

                return new AmpLock(
                    $this->id,
                    function (): void
                    {
                        InMemoryMutexStorage::instance()->unlock($this->id);
                    }
                );
            }
        );
    }
}
