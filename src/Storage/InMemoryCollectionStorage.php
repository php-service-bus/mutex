<?php

/**
 * PHP Mutex implementation.
 *
 * @author  Maksim Masiukevich <dev@async-php.com>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types = 1);

namespace ServiceBus\Mutex\Storage;

use ServiceBus\Mutex\Lock;

/**
 * @internal
 */
final class InMemoryCollectionStorage
{
    /**
     * @psalm-var array<string, \ServiceBus\Mutex\Lock>
     */
    private $localStorage = [];

    /** @var self|null */
    private static $instance = null;

    /**
     * @return self
     */
    public static function instance(): self
    {
        if (self::$instance === null)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function has(string $key): bool
    {
        return isset($this->localStorage[$key]);
    }

    public function remove(string $key): void
    {
        unset($this->localStorage[$key]);
    }

    public function add(string $key, Lock $lock): void
    {
        $this->localStorage[$key] = $lock;
    }

    public function get(string $key): ?Lock
    {
        return $this->localStorage[$key] ?: null;
    }

    /**
     * Reset instance.
     */
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
