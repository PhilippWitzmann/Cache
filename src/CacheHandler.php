<?php

declare(strict_types=1);

namespace PhilippWitzmann\Cache;

use PhilippWitzmann\DateTime\DateTime;
use PhilippWitzmann\DateTime\DateTimeHandler;
use DateTimeZone;

/**
 * Acts as a facade for multiple Cache Solutions.
 *
 * @author Philipp Witzmann <witzmann@contsult.com>
 */
abstract class CacheHandler implements Cache
{
    /** @var DateTimeHandler */
    protected $dateTimeHandler;

    public function __construct(DateTimeHandler $dateTimeHandler)
    {
        $this->dateTimeHandler = $dateTimeHandler;
    }

    /**
     * Persists an entry into the cache for a given amount of time.
     *
     * @param string $key      Any unique string value which is searchable
     * @param mixed  $value    Any value that needs to be stored
     * @param int    $lifetime Amount of seconds the cache entry is valid. Zero for infinite.
     *
     * @return void
     */
    abstract public function set(string $key, $value, int $lifetime = 0): void;

    /**
     * Retrieves an entry from the cache if it is still valid.
     *
     * @param string $key
     *
     * @return mixed|null when no entry was found
     */
    abstract public function get(string $key);

    /**
     * Invalidates a cache entry making it unable to retrieve.
     *
     * @param string $key
     *
     * @return void
     */
    abstract public function invalidate(string $key): void;

    protected function getDatetimeForEndOfLifetime(int $lifeTimeInSeconds): DateTime
    {
        $dateTimeZone = new DateTimeZone('Europe/Berlin');
        $dateTime     = $this->dateTimeHandler->createDateTime($dateTimeZone);

        // Zero is infinite -> so we add 99 years
        if ($lifeTimeInSeconds === 0)
        {
            $lifeTimeInSeconds = 365 * 24 * 60 * 60 * 99;
        }
        $newDateTime = $this->dateTimeHandler->addSeconds($dateTime, $lifeTimeInSeconds);

        return $newDateTime;
    }
}