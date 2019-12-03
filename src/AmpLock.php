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

use function Amp\call;
use Amp\Promise;

/**
 * @internal
 */
final class AmpLock implements Lock
{
    /**
     * Lock identifier.
     *
     * @var string
     */
    private $id;

    /**
     * The function to be called on release or null if the lock has been released.
     *
     * @var \Closure|null
     */
    private $releaser = null;

    public function __construct(string $id, ?\Closure $releaser)
    {
        $this->id       = $id;
        $this->releaser = $releaser;
    }

    /**
     * {@inheritdoc}
     */
    public function released(): bool
    {
        return $this->releaser === null;
    }

    /**
     * {@inheritdoc}
     */
    public function id(): string
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function release(): Promise
    {
        return call(
            function (): \Generator
            {
                if ($this->releaser !== null)
                {
                    $releaser       = $this->releaser;
                    $this->releaser = null;

                    yield call($releaser);
                }
            }
        );
    }

    public function __destruct()
    {
        if ($this->releaser !== null)
        {
            $this->release();
        }
    }
}
