<?php

declare(strict_types=1);

namespace PhilippWitzmann\Cache;

use PhilippWitzmann\DateTime\DateTimeHandler;
use PhilippWitzmann\Testing\TestCase;
use DateTimeZone;
use InvalidArgumentException;
use Predis\Client;

/**
 * Testcase
 *
 * @author Philipp Witzmann <philipp@philippwitzmann.de>
 */
class RedisCacheTest extends TestCase
{
    /** @var RedisCache */
    private $subject;

    /** @var DateTimeHandler */
    private $dateTimeHandler;

    /** @var Client */
    private $mockedClient;

    /**
     * Sets up the Testenvironment for each specific test.
     *
     * @return void
     */
    protected function setUpTest(): void
    {
        $this->dateTimeHandler = new DateTimeHandler();
        $this->mockedClient    = mock(Client::class);
        $this->subject         = new RedisCache($this->dateTimeHandler, $this->mockedClient);
    }

    /**
     * Reverts the environment back its state before these tests started.
     *
     * @return void
     */
    protected function tearDownTest(): void
    {
    }

    public function testGet()
    {
        $key      = 'foo';
        $value    = 'bar';
        $lifetime = 100;
        $this->mockedClient->expects()->set($key, $value);
        $this->mockedClient->expects()->expire($key, $lifetime);
        $this->subject->set($key, $value, $lifetime);

        $this->mockedClient->expects()->exists($key)->andReturn(true);
        $this->mockedClient->expects()->get($key)->andReturn($value);
        $result = $this->subject->get($key);

        $this->assertSame($value, $result);
    }

    public function testGetWithInvalidedTime()
    {
        $key      = 'foo';
        $value    = 'bar';
        $lifetime = 100;
        $this->mockedClient->expects()->set($key, $value);
        $this->mockedClient->expects()->expire($key, $lifetime);
        $this->subject->set($key, $value, $lifetime);

        $this->mockedClient->expects()->exists($key)->andReturn(false);
        $result = $this->subject->get($key);

        $this->assertSame(null, $result);
    }

    public function testSetNegativeLifetime()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(1532673868);
        $this->subject->set('foo', 'bar', -1);
    }

    public function testInvalidate()
    {
        $key      = 'foo';
        $value    = 'bar';
        $lifetime = 100;
        $this->mockedClient->expects()->set($key, $value);
        $this->mockedClient->expects()->expire($key, $lifetime);
        $this->subject->set($key, $value, $lifetime);

        $this->mockedClient->expects()->exists($key)->andReturn(true);
        $this->mockedClient->expects()->get($key)->andReturn($value);
        $this->assertSame($value, $this->subject->get($key));

        $this->mockedClient->expects()->expire($key, 0);
        $this->subject->invalidate($key);
        $this->mockedClient->expects()->exists($key)->andReturn(false);

        $result = $this->subject->get($key);

        $this->assertSame(null, $result);
    }

    public function testSet()
    {
        $key      = 'foo';
        $value    = 'bar';
        $lifetime = 100;
        $this->mockedClient->expects()->set($key, $value);
        $this->mockedClient->expects()->expire($key, $lifetime);
        $result = $this->subject->set($key, $value, $lifetime);

        $this->assertEmpty($result);
    }

    public function testSetInfiniteLifetime()
    {
        $key      = 'foo';
        $value    = 'bar';
        $lifetime = 0;
        $this->mockedClient->expects()->set($key, $value);
        $this->subject->set($key, $value, $lifetime);

        $this->mockedClient->expects()->exists($key)->andReturn(true);
        $this->mockedClient->expects()->get($key)->andReturn($value);

        $result = $this->subject->get($key);

        $this->assertSame($value, $result);
    }
}