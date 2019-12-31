<?php

declare(strict_types=1);

namespace PhilippWitzmann\Cache;

use PhilippWitzmann\DateTime\DateTimeHandler;
use PhilippWitzmann\Testing\TestCase;
use DateTimeZone;
use InvalidArgumentException;

/**
 * Testcase
 *
 * @author Philipp Witzmann <philipp@philippwitzmann.de>
 */
class ArrayCacheTest extends TestCase
{
    /** @var ArrayCache */
    private $subject;

    /** @var DateTimeHandler */
    private $dateTimeHandler;

    /**
     * Sets up the Testenvironment for each specific test.
     *
     * @return void
     */
    protected function setUpTest(): void
    {
        $this->dateTimeHandler = new DateTimeHandler();
        $this->subject         = new ArrayCache($this->dateTimeHandler);
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
        $this->subject->set($key, $value, $lifetime);

        $result = $this->subject->get($key);

        $this->assertSame($value, $result);
    }

    public function testGetWithInvalidedTime()
    {
        $key      = 'foo';
        $value    = 'bar';
        $lifetime = 100;
        $this->subject->set($key, $value, $lifetime);

        $timezone          = new DateTimeZone('Europe/Berlin');
        $dateTime          = $this->dateTimeHandler->createDateTime($timezone);
        $dateTimeInThePast = $this->dateTimeHandler->addSeconds($dateTime, $lifetime + 1);
        $this->dateTimeHandler->setTest($dateTimeInThePast);

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
        $this->subject->set($key, $value, $lifetime);
        $this->assertSame($value, $this->subject->get($key));
        $this->subject->invalidate($key);

        $result = $this->subject->get($key);

        $this->assertSame(null, $result);
    }

    public function testSet()
    {
        $key      = 'foo';
        $value    = 'bar';
        $lifetime = 100;
        $result   = $this->subject->set($key, $value, $lifetime);

        $this->assertEmpty($result);
    }

    public function testSetInfiniteLifetime()
    {
        $key      = 'foo';
        $value    = 'bar';
        $lifetime = 0;
        $this->subject->set($key, $value, $lifetime);

        $timezone          = new DateTimeZone('Europe/Berlin');
        $dateTime          = $this->dateTimeHandler->createDateTime($timezone);
        $dateTimeInThePast = $this->dateTimeHandler->addSeconds($dateTime, 100);
        $this->dateTimeHandler->setTest($dateTimeInThePast);

        $result = $this->subject->get($key);

        $this->assertSame($value, $result);
    }
}