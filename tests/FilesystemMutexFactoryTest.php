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

use PHPUnit\Framework\TestCase;
use ServiceBus\Mutex\FilesystemMutex;
use ServiceBus\Mutex\FilesystemMutexFactory;

/**
 *
 */
final class FilesystemMutexFactoryTest extends TestCase
{
    /**
     * @test
     *
     * @throws \Throwable
     *
     * @return void
     */
    public function create(): void
    {
        $mutex = (new FilesystemMutexFactory(\sys_get_temp_dir()))->create(__CLASS__);

        static::assertInstanceOf(FilesystemMutex::class, $mutex);
    }
}
