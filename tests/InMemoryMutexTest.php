<?php

/**
 * PHP Mutex implementation.
 *
 * @author  Maksim Masiukevich <dev@async-php.com>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types = 1);

namespace ServiceBus\Mutex\Tests;

use Amp\Loop;
use PHPUnit\Framework\TestCase;
use ServiceBus\Mutex\InMemoryMutex;
use ServiceBus\Mutex\Lock;
use ServiceBus\Mutex\Storage\InMemoryMutexStorage;

/**
 *
 */
final class InMemoryMutexTest extends TestCase
{
    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        InMemoryMutexStorage::instance()->reset();
    }

    /**
     * @test
     *
     * @return void
     * @throws \Throwable
     *
     */
    public function acquire(): void
    {
        Loop::run(
            function(): \Generator
            {
                $mutex   = new InMemoryMutex(__CLASS__);
                $promise = $mutex->acquire();

                static::assertTrue(InMemoryMutexStorage::instance()->has(__CLASS__));

                /** @var Lock $lock */
                $lock = yield $promise;

                yield $lock->release();

                static::assertFalse(InMemoryMutexStorage::instance()->has(__CLASS__));

                static::assertSame(__CLASS__, $lock->id());
                static::assertTrue($lock->released());

                Loop::stop();
            }
        );
    }
}
