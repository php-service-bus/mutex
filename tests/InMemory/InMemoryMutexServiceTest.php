<?php

/** @noinspection PhpUnhandledExceptionInspection */

/**
 * PHP Mutex implementation.
 *
 * @author  Maksim Masiukevich <contacts@desperado.dev>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace ServiceBus\Mutex\Tests\InMemory;

use Amp\Loop;
use Amp\Promise;
use Amp\Success;
use PHPUnit\Framework\TestCase;
use ServiceBus\Mutex\InMemory\InMemoryMutexService;
use ServiceBus\Mutex\InMemory\InMemoryMutexStorage;

final class InMemoryMutexServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();

        InMemoryMutexStorage::instance()->reset();
    }

    /**
     * @test
     */
    public function acquire(): void
    {
        Loop::run(
            static function (): \Generator
            {
                $id           = \sha1(uniqid("test", true));
                $mutexService = new InMemoryMutexService();

                yield $mutexService->withLock(
                    $id,
                    static function () use ($id): Promise
                    {
                        self::assertTrue(InMemoryMutexStorage::instance()->has($id));

                        return new Success();
                    }
                );

                self::assertFalse(InMemoryMutexStorage::instance()->has($id));

                Loop::stop();
            }
        );
    }
}
