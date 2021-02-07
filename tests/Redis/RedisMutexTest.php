<?php /** @noinspection PhpUnhandledExceptionInspection */

/**
 * PHP Mutex implementation.
 *
 * @author  Maksim Masiukevich <contacts@desperado.dev>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types = 1);

namespace ServiceBus\Mutex\Tests\Redis;

use Amp\Loop;
use Amp\Redis\Config;
use Amp\Redis\Redis;
use Amp\Redis\RemoteExecutor;
use PHPUnit\Framework\TestCase;
use ServiceBus\Mutex\Lock;
use ServiceBus\Mutex\Redis\RedisMutexFactory;

/**
 *
 */
final class RedisMutexTest extends TestCase
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
                $mutex = (new RedisMutexFactory($this->client))->create(__CLASS__);

                /** @var Lock $lock */
                $lock = yield $mutex->acquire();

                /** @var bool $has */
                $has = yield $this->client->has(__CLASS__);

                self::assertTrue($has);

                yield $lock->release();

                /** @var bool $has */
                $has = yield $this->client->has(__CLASS__);

                self::assertFalse($has);

                self::assertSame(__CLASS__, $lock->id());
                self::assertTrue($lock->released());

                Loop::stop();
            }
        );
    }
}
