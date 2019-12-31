<?php

declare(strict_types=1);

namespace PhilippWitzmann\Cache;

use PhilippWitzmann\DateTime\DateTimeHandler;
use InvalidArgumentException;
use Predis\Client;

/**
 * This class implements the redis cache.
 *
 * @author Philipp Witzmann <philipp@philippwitzmann.de>
 */
class RedisCache extends CacheHandler implements Cache
{
    /** @var Client */
    private $client;

    public function __construct(DateTimeHandler $dateTimeHandler, Client $client)
    {
        parent::__construct($dateTimeHandler);
        $this->client = $client;
    }
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

        $this->client->set($key, $value);
        if ($lifetime !== 0)
        {
            $this->client->expire($key, $lifetime);
        }
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
        if (!$this->client->exists($key))
        {
            return null;
        }

        return $this->client->get($key);
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
        $this->client->expire($key, 0);
    }
}