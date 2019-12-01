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

use function Amp\call;
use Amp\Delayed;
use Amp\Promise;
use ServiceBus\Mutex\Storage\InMemoryMutexStorage;

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
    private string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function __destruct()
    {
        InMemoryMutexStorage::instance()->unlock($this->id);
    }

    /**
     * @psalm-suppress MixedTypeCoercion
     *
     * {@inheritdoc}
     */
    public function acquire(): Promise
    {
        return call(
            function (): \Generator
            {
                while (InMemoryMutexStorage::instance()->has($this->id))
                {
                    yield new Delayed(self::LATENCY_TIMEOUT);
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
