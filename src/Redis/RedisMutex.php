<?php

/**
 * PHP Mutex implementation.
 *
 * @author  Maksim Masiukevich <contacts@desperado.dev>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types = 0);

namespace ServiceBus\Mutex\Redis;

use Amp\Promise;
use Amp\Redis\Redis;
use ServiceBus\Mutex\AmpLock;
use ServiceBus\Mutex\Exceptions\SyncException;
use ServiceBus\Mutex\Mutex;
use function Amp\call;
use function Amp\delay;

/**
 *
 */
final class RedisMutex implements Mutex
{
    private const LATENCY_TIMEOUT = 10;

    /**
     * @var Redis
     */
    private $client;

    /**
     * @var string
     */
    private $id;

    public function __construct(Redis $client, string $id)
    {
        $this->client = $client;
        $this->id     = $id;
    }

    public function acquire(): Promise
    {
        return call(
            function (): \Generator
            {
                try
                {
                    while (yield $this->client->has($this->id))
                    {
                        yield delay(self::LATENCY_TIMEOUT);
                    }

                    yield $this->client->set($this->id, 'lock');

                    return new AmpLock(
                        $this->id,
                        function (): \Generator
                        {
                            yield $this->client->delete($this->id);
                        }
                    );
                }
                catch (\Throwable $throwable)
                {
                    throw SyncException::fromThrowable($throwable);
                }
            }
        );
    }
}
