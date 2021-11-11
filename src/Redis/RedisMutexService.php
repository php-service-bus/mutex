<?php

/**
 * PHP Mutex implementation.
 *
 * @author  Maksim Masiukevich <contacts@desperado.dev>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=0);

namespace ServiceBus\Mutex\Redis;

use Amp\Promise;
use Amp\Redis\Redis;
use Amp\Redis\SetOptions;
use ServiceBus\Mutex\MutexService;
use function Amp\call;
use function Amp\delay;

final class RedisMutexService implements MutexService
{
    private const LATENCY_TIMEOUT = 100;
    /** Lock lifetime in seconds */
    private const DEFAULT_LOCK_LIFETIME = 120;

    /**
     * @var Redis
     */
    private $client;

    /**
     * @var SetOptions
     */
    private $lockOptions;

    public function __construct(Redis $client, int $lockLifetime = self::DEFAULT_LOCK_LIFETIME)
    {
        $this->client      = $client;
        $this->lockOptions = (new SetOptions())->withTtl($lockLifetime);
    }

    public function withLock(string $id, callable $code): Promise
    {
        return call(
            function () use ($id, $code): \Generator
            {
                try
                {
                    while (yield $this->client->has($id))
                    {
                        yield delay(self::LATENCY_TIMEOUT);
                    }

                    yield $this->client->set($id, 'lock', $this->lockOptions);
                    yield call($code);
                }
                finally
                {
                    yield $this->client->delete($id);
                }
            }
        );
    }
}
