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
use Amp\Success;
use ServiceBus\Mutex\Storage\InMemoryCollectionStorage;

/**
 *
 */
final class InMemoryLockCollection implements LockCollection
{
    public function __destruct()
    {
        InMemoryCollectionStorage::instance()->reset();
    }

    /**
     * @inheritDoc
     */
    public function has(string $key): Promise
    {
        return new Success(InMemoryCollectionStorage::instance()->has($key));
    }

    /**
     * @inheritDoc
     */
    public function place(string $key, Lock $lock): Promise
    {
        InMemoryCollectionStorage::instance()->add($key, $lock);

        return new Success();
    }

    /**
     * @inheritDoc
     */
    public function extract(string $key): Promise
    {
        $lock = InMemoryCollectionStorage::instance()->get($key);

        InMemoryCollectionStorage::instance()->remove($key);

        return new Success($lock);
    }
}
