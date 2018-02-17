<?php

namespace TimurFlush\Phalclate\Storage;

use TimurFlush\Phalclate\StorageInterface;

/**
 * Class Memory
 * @package TimurFlush\Phalclate
 * @author Timur Flush
 * @version 1.0.3
 *
 * @property array $_pool
 */
class Memory implements StorageInterface
{
    /**
     * Memory storage.
     *
     * @var array = []
     */
    private $_cache = [];

    /**
     * Push data to memory storage.
     *
     * @param $key
     * @param $value
     */
    public function save($key, $value) : void
    {
        $this->_cache[$key] = $value;
    }

    /**
     * Get data from memory storage.
     *
     * @param null $key
     * @return mixed
     */
    public function get($key = null)
    {
        if ($key === null)
            return $this->_cache;

        return $this->_cache[$key] ?? null;
    }

    /**
     * Checks for existence of data in memory storage.
     *
     * @param $key
     * @return bool
     */
    public function exists($key) : bool
    {
        return isset($this->_cache[$key]);
    }

    /**
     * Removes data from memory storage.
     *
     * @param $key
     * @return void
     */
    public function remove($key): void
    {
        unset($this->_cache[$key]);
    }

    /**
     * Clears memory storage.
     *
     * @return void
     */
    public function flush(): void
    {
        $this->_cache = [];
    }
}