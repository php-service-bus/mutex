<?php /** @noinspection PhpUnhandledExceptionInspection */

/**
 * PHP Mutex implementation.
 *
 * @author  Maksim Masiukevich <contacts@desperado.dev>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types = 1);

namespace ServiceBus\Mutex\Tests\InMemory;

use Amp\Loop;
use PHPUnit\Framework\TestCase;
use ServiceBus\Mutex\InMemory\InMemoryMutex;
use ServiceBus\Mutex\InMemory\InMemoryMutexStorage;
use ServiceBus\Mutex\Lock;

/**
 *
 */
final class InMemoryMutexTest extends TestCase
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
                $mutex   = new InMemoryMutex(__CLASS__);
                $promise = $mutex->acquire();

                self::assertTrue(InMemoryMutexStorage::instance()->has(__CLASS__));

                /** @var Lock $lock */
                $lock = yield $promise;

                yield $lock->release();

                self::assertFalse(InMemoryMutexStorage::instance()->has(__CLASS__));

                self::assertSame(__CLASS__, $lock->id());
                self::assertTrue($lock->released());

                Loop::stop();
            }
        );
    }
}
