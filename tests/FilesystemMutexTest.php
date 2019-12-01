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

use function Amp\Promise\wait;
use Amp\Loop;
use PHPUnit\Framework\TestCase;
use ServiceBus\Mutex\Exceptions\SyncException;
use ServiceBus\Mutex\FilesystemMutex;
use ServiceBus\Mutex\Lock;

/**
 *
 */
final class FilesystemMutexTest extends TestCase
{
    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        @\unlink(\sys_get_temp_dir() . '/mutex.test');
    }

    /**
     * @test
     *
     * @throws \Throwable
     */
    public function acquire(): void
    {
        Loop::run(
            function (): \Generator
            {
                $mutexFile = \sys_get_temp_dir() . '/mutex.test';

                @\unlink($mutexFile);

                $mutex = new FilesystemMutex(__CLASS__, $mutexFile);

                /** @var Lock $lock */
                $lock = yield $mutex->acquire();

                $mutex->acquire();

                static::assertFileExists($mutexFile);

                yield $lock->release();

                unset($lock, $mutex);

                static::assertFileNotExists($mutexFile);

                Loop::stop();
            }
        );
    }

    /**
     * @test
     *
     * @throws \Throwable
     */
    public function writeToIncorrectDirectory(): void
    {
        $this->expectException(SyncException::class);

        $mutex = new FilesystemMutex(__CLASS__, '/qwertyRoot');

        wait($mutex->acquire());
    }
}
