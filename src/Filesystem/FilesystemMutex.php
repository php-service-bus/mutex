<?php

/**
 * PHP Mutex implementation.
 *
 * @author  Maksim Masiukevich <dev@async-php.com>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types = 1);

namespace ServiceBus\Mutex\Filesystem;

use ServiceBus\Mutex\AmpLock;
use ServiceBus\Mutex\Mutex;
use function Amp\asyncCall;
use function Amp\call;
use function Amp\File\get;
use function Amp\File\put;
use function Amp\File\unlink;
use Amp\Delayed;
use Amp\Promise;
use ServiceBus\Mutex\Exceptions\SyncException;

/**
 * It can be used when several processes are running within the same host.
 *
 * @internal Created by factory (FilesystemMutexFactory::create())
 *
 * @see FilesystemMutexFactory
 */
final class FilesystemMutex implements Mutex
{
    private const LATENCY_TIMEOUT = 50;

    /**
     * Mutex identifier.
     *
     * @var string
     */
    private $id;

    /**
     * Barrier file path.
     *
     * @var string
     */
    private $filePath;

    /**
     * Release handler.
     *
     * @var \Closure
     */
    private $release;

    public function __construct(string $id, string $filePath)
    {
        $this->id       = $id;
        $this->filePath = $filePath;
        $this->release  = function (): \Generator
        {
            try
            {
                yield unlink($this->filePath);
            }
            // @codeCoverageIgnoreStart
            catch (\Throwable $throwable)
            {
                /** Not interests */
            }
            // @codeCoverageIgnoreEnd
        };
    }

    public function __destruct()
    {
        asyncCall($this->release);
    }

    /**
     * {@inheritdoc}
     */
    public function acquire(): Promise
    {
        return call(
            function (): \Generator
            {
                try
                {
                    /** @var bool $exists */
                    $exists = yield from self::hasLockFile($this->filePath);

                    while ($exists)
                    {
                        yield new Delayed(self::LATENCY_TIMEOUT);
                    }

                    yield put($this->filePath, '');

                    return new AmpLock($this->id, $this->release);
                }
                catch (\Throwable $throwable)
                {
                    throw SyncException::fromThrowable($throwable);
                }
            }
        );
    }

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
