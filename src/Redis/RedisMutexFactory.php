<?php

/**
 * PHP Mutex implementation.
 *
 * @author  Maksim Masiukevich <dev@async-php.com>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types = 1);

namespace ServiceBus\Mutex\Redis;

use Amp\Redis\Redis;
use ServiceBus\Mutex\Mutex;
use ServiceBus\Mutex\MutexFactory;

/**
 *
 */
final class RedisMutexFactory implements MutexFactory
{
    /** @var Redis */
    private $client;

    public function __construct(Redis $client)
    {
        $this->client = $client;
    }

    /**
     * @inheritDoc
     */
    public function create(string $id): Mutex
    {
        return new RedisMutex($this->client, $id);
    }
}
