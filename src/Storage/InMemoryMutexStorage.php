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

/**
 * @internal
 */
final class InMemoryMutexStorage
{
    /**
     * @var array<string, bool>
     */
    private $localStorage = [];

    /**
     * @var self|null
     */
    private static $instance;

    /**
     * @return self
     */
    public static function instance(): self
    {
        if (null === self::$instance)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param string $key
     *
     * @return void
     */
    public function lock(string $key): void
    {
        $this->localStorage[$key] = true;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($this->localStorage[$key]);
    }

    /**
     * @param string $key
     *
     * @return void
     */
    public function unlock(string $key): void
    {
        unset($this->localStorage[$key]);
    }

    /**
     * Reset instance.
     *
     * @return void
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
