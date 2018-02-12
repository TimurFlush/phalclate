<?php

namespace TimurFlush\Phalclate;

/**
 * Class StorageInterface
 * @package TimurFlush\Phalclate
 */
interface StorageInterface
{
    /**
     * @param $key
     * @param $value
     * @return mixed
     */
    public function save($key, $value) : void;

    /**
     * @param null $key
     * @return mixed|\stdClass
     */
    public function get($key = null);

    /**
     * @param $key
     * @return mixed
     */
    public function exists($key) : bool;

    /**
     * @param $key
     * @return void
     */
    public function remove($key) : void;

    /**
     * @return void
     */
    public function flush() : void;
}