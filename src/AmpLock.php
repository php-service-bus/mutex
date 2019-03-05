<?php

/**
 * PHP Mutex implementation.
 *
 * @author  Maksim Masiukevich <dev@async-php.com>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types = 1);

namespace ServiceBus\Mutex;

use function Amp\asyncCall;

/**
 *
 */
final class AmpLock implements Lock
{
    /**
     * The function to be called on release or null if the lock has been released.
     *
     * @var callable|null
     */
    private $releaser;

    /**
     * @param callable|null $releaser
     */
    public function __construct(?callable $releaser)
    {
        $this->releaser = $releaser;
    }

    /**
     * {@inheritdoc}
     */
    public function released(): bool
    {
        return null === $this->releaser;
    }

    /**
     * {@inheritdoc}
     */
    public function release(): void
    {
        if (null === $this->releaser)
        {
            return;
        }

        $releaser       = $this->releaser;
        $this->releaser = null;

        asyncCall($releaser);
    }

    public function __destruct()
    {
        if (null !== $this->releaser)
        {
            $this->release();
        }
    }
}
