<?php

declare(strict_types=1);

namespace PhilippWitzmann\Cache;

/**
 * All Cache Implementation need to implement this. It controls the basic functionalities
 * every Cache needs to have.
 *
 * @author Philipp Witzmann <philipp@philippwitzmann.de>
 */
interface Cache
{
    /**
     * Persists an entry into the cache for a given amount of time.
     *
     * @param string $key      Any unique string value which is searchable
     * @param mixed  $value    Any value that needs to be stored
     * @param int    $lifetime Amount of seconds the cache entry is valid. Zero for infinite.
     *
     * @return void
     */
    public function set(string $key, $value, int $lifetime = 0): void;

    /**
     * Retrieves an entry from the cache if it is still valid.
     *
     * @param string $key
     *
     * @return mixed|null when no entry was found
     */
    public function get(string $key);

    /**
     * Invalidates a cache entry making it unable to retrieve.
     *
     * @param string $key
     *
     * @return void
     */
    public function invalidate(string $key): void;
}