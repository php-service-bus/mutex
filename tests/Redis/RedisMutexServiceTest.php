<?php

/** @noinspection PhpUnhandledExceptionInspection */

/**
 * PHP Mutex implementation.
 *
 * @author  Maksim Masiukevich <contacts@desperado.dev>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace ServiceBus\Mutex\Tests\Redis;

use Amp\Loop;
use Amp\Promise;
use Amp\Redis\Config;
use Amp\Redis\Redis;
use Amp\Redis\RemoteExecutor;
use PHPUnit\Framework\TestCase;
use ServiceBus\Mutex\Redis\RedisMutexService;
use function Amp\call;

final class RedisMutexServiceTest extends TestCase
{
    /**
     * @var Redis
     */
    private $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = new Redis(
            new RemoteExecutor(
                Config::fromUri((string) \getenv('REDIS_CONNECTION_DSN'))
            )
        );
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->client);
    }

    /**
     * @test
     */
    public function acquire(): void
    {
        Loop::run(
            function (): \Generator
            {
                $id           = \sha1(uniqid("test", true));
                $mutexService = new RedisMutexService($this->client);

                yield $mutexService->withLock(
                    $id,
                    function () use ($id): Promise
                    {
                        return call(
                            function () use ($id): \Generator
                            {
                                self::assertTrue(yield $this->client->has($id));
                            }
                        );
                    }
                );

                self::assertFalse(yield $this->client->has($id));

                Loop::stop();
            }
        );
    }
}
