<?php

namespace TimurFlush\Phalclate;

/**
 * Class Storage
 * @package TimurFlush\Phalclate
 */
class Storage implements StorageInterface
{
    /**
     * @var array = []
     */
    private $_pool = [];

    /**
     * @param $key
     * @param $value
     */
    public function save($key, $value) : void
    {
        $this->_pool[$key] = $value;
    }

    /**
     * @param null $key
     * @return mixed
     */
    public function get($key = null)
    {
        if ($key === null)
            return $this->_pool;

        return $this->_pool[$key] ?? null;
    }

    /**
     * @param $key
     * @return bool
     */
    public function exists($key) : bool
    {
        return isset($this->_pool[$key]);
    }

    /**
     * @param $key
     * @return void
     */
    public function remove($key): void
    {
        unset($this->_pool[$key]);
    }

    /**
     * @return void
     */
    public function flush(): void
    {
        $this->_pool = [];
    }
}