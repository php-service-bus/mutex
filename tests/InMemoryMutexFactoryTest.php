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
use ServiceBus\Mutex\InMemoryMutex;
use ServiceBus\Mutex\InMemoryMutexFactory;

/**
 *
 */
final class InMemoryMutexFactoryTest extends TestCase
{
    /**
     * @test
     *
     * @throws \Throwable
     */
    public function create(): void
    {
        $mutex = (new InMemoryMutexFactory())->create(__CLASS__);

        static::assertInstanceOf(InMemoryMutex::class, $mutex);
    }
}
