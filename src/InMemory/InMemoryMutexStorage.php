<?php

/**
 * PHP Mutex implementation.
 *
 * @author  Maksim Masiukevich <contacts@desperado.dev>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types = 0);

namespace ServiceBus\Mutex\InMemory;

/**
 * @internal
 */
final class InMemoryMutexStorage
{
    /**
     * @psalm-var array<string, bool>
     */
    private $localStorage = [];

    /**
     * @var self|null
     */
    private static $instance;

    public static function instance(): self
    {
        if (self::$instance === null)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function lock(string $key): void
    {
        $this->localStorage[$key] = true;
    }

    public function has(string $key): bool
    {
        return isset($this->localStorage[$key]);
    }

    public function unlock(string $key): void
    {
        unset($this->localStorage[$key]);
    }

    public function reset(): void
    {
        self::$instance = null;
    }

    /**
     * @codeCoverageIgnore
     */
    private function __clone()
    {
    }

    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }
}
