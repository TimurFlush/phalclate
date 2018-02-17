<?php

namespace TimurFlush\Phalclate;

/**
 * Class StorageInterface
 * @package TimurFlush\Phalclate
 * @author Timur Flush
 * @version 1.0.2
 */
interface StorageInterface
{
    /**
     * Push data to storage.
     *
     * @param $key
     * @param $value
     * @return mixed
     */
    public function save($key, $value) : void;

    /**
     * Get data from storage.
     *
     * @param null $key
     * @return mixed|\stdClass
     */
    public function get($key = null);

    /**
     * Checks for existence of data in storage.
     *
     * @param $key
     * @return mixed
     */
    public function exists($key) : bool;

    /**
     * Removes data from storage.
     *
     * @param $key
     * @return void
     */
    public function remove($key) : void;

    /**
     * Clears storage.
     *
     * @return void
     */
    public function flush() : void;
}