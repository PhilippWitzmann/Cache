<?php

declare(strict_types=1);

namespace PhilippWitzmann\Cache;

use DateTimeZone;
use InvalidArgumentException;

/**
 * This class implements an array cache. Array Cache is not shareable between session and processes!
 *
 * @author Philipp Witzmann <witzmann@contsult.com>
 */
class ArrayCache extends CacheHandler implements Cache
{
    /** @var ArrayCacheEntry[] */
    private $cache;

    /**
     * Persists an entry into the cache for a given amount of time.
     *
     * @param string $key      Any unique string value which is searchable
     * @param mixed  $value    Any value that needs to be stored
     * @param int    $lifetime Amount of seconds the cache entry is valid. Zero for infinite.
     *
     * @throws InvalidArgumentException when negative lifetime was given
     *
     * @return void
     */
    public function set(string $key, $value, int $lifetime = 0): void
    {
        if ($lifetime < 0)
        {
            throw new InvalidArgumentException('Lifetimes smaller than 0 second are not allowed', 1532673868);
        }

        $validUntil = $this->getDatetimeForEndOfLifetime($lifetime);
        $entry      = new ArrayCacheEntry($key, $value, $validUntil);

        $this->cache[$key] = $entry;
    }

    /**
     * Retrieves an entry from the cache if it is still valid.
     *
     * @param string $key
     *
     * @return mixed|null when no entry was found
     */
    public function get(string $key)
    {
        if (array_key_exists($key, $this->cache) === false)
        {
            return null;
        }
        $arrayCacheEntry = $this->cache[$key];

        $timezone = new DateTimeZone('Europe/Berlin');
        $dateTime = $this->dateTimeHandler->createDateTime($timezone);

        $timeDiff = $this->dateTimeHandler->diff($dateTime, $arrayCacheEntry->getValidUntil());

        if ($timeDiff->invert)
        {
            $this->invalidate($key);
            return $this->get($key);
        }

        return $arrayCacheEntry->getValue();
    }

    /**
     * Invalidates a cache entry making it unable to retrieve.
     *
     * @param string $key
     *
     * @return void
     */
    public function invalidate(string $key): void
    {
        unset($this->cache[$key]);
    }
}