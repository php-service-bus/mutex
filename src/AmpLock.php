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
     * @var callable|null
     */
    private $releaser;

    /**
     * @param string        $id
     * @param callable|null $releaser
     */
    public function __construct(string $id, ?callable $releaser)
    {
        $this->id       = $id;
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
    public function id(): string
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function release(): Promise
    {
        /** @psalm-suppress MixedTypeCoercion */
        return call(
            function(): \Generator
            {
                if (null !== $this->releaser)
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
        if (null !== $this->releaser)
        {
            $this->release();
        }
    }
}
