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
 */
final class InMemoryMutex implements Mutex
{
    const LATENCY_TIMEOUT = 10;

    /**
     * @var string
     */
    private $key;

    /**
     * @param string $key
     */
    public function __construct(string $key)
    {
        $this->key = $key;
    }

    public function __destruct()
    {
        InMemoryMutexStorage::instance()->unlock($this->key);
    }

    /**
     * @psalm-suppress MixedTypeCoercion
     *
     * {@inheritdoc}
     */
    public function acquire(): Promise
    {
        return call(
            function(): \Generator
            {
                while (InMemoryMutexStorage::instance()->has($this->key))
                {
                    yield new Delayed(self::LATENCY_TIMEOUT);
                }

                InMemoryMutexStorage::instance()->lock($this->key);

                return new AmpLock(
                    function(): void
                    {
                        InMemoryMutexStorage::instance()->unlock($this->key);
                    }
                );
            }
        );
    }
}
