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
use function Amp\call;
use function Amp\File\get;
use function Amp\File\touch;
use function Amp\File\unlink;
use Amp\Delayed;
use Amp\Promise;
use ServiceBus\Mutex\Exceptions\SyncException;

/**
 * It can be used when several processes are running within the same host.
 */
final class FileMutex implements Mutex
{
    const LATENCY_TIMEOUT = 50;

    /**
     * @var string
     */
    private $filePath;

    /**
     * Release handler.
     *
     * @var \Closure
     */
    private $release;

    /**
     * @param string $filePath
     */
    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
        $this->release  = function(): \Generator
        {
            try
            {
                yield unlink($this->filePath);
            }
            catch (\Throwable $throwable)
            {
                /** Not interests */
            }
        };
    }

    public function __destruct()
    {
        asyncCall($this->release);
    }

    /**
     * @psalm-suppress MixedTypeCoercion
     *
     * {@inheritdoc}
     */
    public function acquire(): Promise
    {
        return call(
            function(): \Generator
            {
                try
                {
                    while (yield from self::hasLockFile($this->filePath))
                    {
                        yield new Delayed(self::LATENCY_TIMEOUT);
                    }

                    yield touch($this->filePath);

                    return new AmpLock($this->release);
                }
                catch (\Throwable $throwable)
                {
                    throw SyncException::fromThrowable($throwable);
                }
            }
        );
    }

    /**
     * @param string $path
     *
     * @return \Generator
     */
    private static function hasLockFile(string $path): \Generator
    {
        try
        {
            yield get($path);

            return true;
        }
        catch (\Throwable $throwable)
        {
            /** Not interests */
        }

        return false;
    }
}
