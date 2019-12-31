<?php

declare(strict_types=1);

namespace PhilippWitzmann\Cache;

use PhilippWitzmann\DateTime\DateTime;

/**
 * A simple data object to store value which should be persisted into the Array Cache.
 *
 * @internal
 * @author Philipp Witzmann <philipp@philippwitzmann.de>
 */
class ArrayCacheEntry
{
    /** @var string */
    private $key;

    /** @var mixed */
    private $value;

    /** @var DateTime */
    private $validUntil;

    /**
     * @param string   $key
     * @param mixed    $value
     * @param DateTime $validUntil
     */
    public function __construct(string $key, $value, DateTime $validUntil)
    {
        $this->key        = $key;
        $this->value      = $value;
        $this->validUntil = $validUntil;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    public function getValidUntil(): DateTime
    {
        return $this->validUntil;
    }
}