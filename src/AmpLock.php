<?php

/**
 * PHP Mutex implementation.
 *
 * @author  Maksim Masiukevich <contacts@desperado.dev>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types = 0);

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
    private $releaser;

    public function __construct(string $id, ?\Closure $releaser)
    {
        $this->id       = $id;
        $this->releaser = $releaser;
    }

    public function released(): bool
    {
        return $this->releaser === null;
    }

    public function id(): string
    {
        return $this->id;
    }

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
