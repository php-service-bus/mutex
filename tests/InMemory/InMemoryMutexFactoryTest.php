<?php

/**
 * PHP Mutex implementation.
 *
 * @author  Maksim Masiukevich <contacts@desperado.dev>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types = 1);

namespace ServiceBus\Mutex\Tests\InMemory;

use PHPUnit\Framework\TestCase;
use ServiceBus\Mutex\InMemory\InMemoryMutex;
use ServiceBus\Mutex\InMemory\InMemoryMutexFactory;

/**
 *
 */
final class InMemoryMutexFactoryTest extends TestCase
{
    /** @test */
    public function create(): void
    {
        $mutex = (new InMemoryMutexFactory())->create(__CLASS__);

        static::assertInstanceOf(InMemoryMutex::class, $mutex);
    }
}
