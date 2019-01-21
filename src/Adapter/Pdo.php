<?php
/*
 ***********************************************************************
 * Copyright (c) 2018 - present, Timur Flush. All rights reserved.
 ***********************************************************************
 * Author: Timur Flush <flush02@tutanota.com> <https://github.com/timurflush>
 ***********************************************************************
*/
namespace TimurFlush\Phalclate\Adapter;

use TimurFlush\Phalclate\Adapter;

abstract class Pdo extends Adapter
{
    /**
     * @var \PDO
     */
    protected $_pdo;

    /**
     * @var string
     */
    protected $_tableName;

    /**
     * @var bool
     */
    protected $_isConnected = false;

    /**
     * Pdo constructor.
     * @param array $options
     * @throws \Exception The PDO extension is not loaded.
     * @throws \Exception The 'tableName' option must be passed to constructor and be string.
     * @throws \Exception Passed PDO object is not %DRIVER_NAME% driver.
     */
    public function __construct(array $options)
    {
        if (!isset($options['tableName']) || !is_string($options['tableName'])) {
            throw new \Exception('Parameter \'tableName\' must be passed to constructor and be string.');
        }

        $this->_tableName = $options['tableName'];
        unset($options['tableName']);

        if (($pdo = $this->detectPdoObject($options)) !== null) {
            if ($pdo->getAttribute(\PDO::ATTR_DRIVER_NAME) !== $this->getPdoDriver()) {
                throw new \Exception('Passed PDO object is not ' . $this->getPdoDriver() . ' driver.');
            }

            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            $this->_isConnected = true;
            $this->_pdo = $pdo;
        } else {
            $this->_isConnected = $this->connect($options);
        }
    }

    /**
     * Get PDO driver.
     *
     * @return string
     */
    abstract public function getPdoDriver(): string;

    /**
     * Connect to database.
     *
     * @see https://github.com/phalcon/cphalcon/blob/master/phalcon/db/adapter/pdo.zep#L247
     * @param array $descriptor
     * @return bool
     */
    protected function connect(array $descriptor)
    {
        $username = '';
        $password = '';
        $options = $dsnAttributes = [];

        /**
         * Check for a username or use null as default
         */
        if (isset($descriptor['username'])) {
            if (is_string($descriptor['username'])) {
                $username = $descriptor['username'];
            }
            unset($descriptor['username']);
        }

        /**
         * Check for a password or use null as default
         */
        if (isset($descriptor['password'])) {
            if (is_string($descriptor['password'])) {
                $password = $descriptor['password'];
            }
            unset($descriptor['password']);
        }

        /**
         * Check if the developer has defined custom options or create one from scratch
         */
        if (isset($descriptor['options'])) {
            if (is_array($descriptor['options'])) {
                $options = $descriptor['options'];
            }
            unset($descriptor['options']);
        }

        /**
         * Check for \PDO::XXX class constant aliases
         */
        foreach ($options as $key => $value) {
            if (is_string($key) && defined('\PDO::' . strtoupper($key))) {
                $options[constant('\PDO::' . strtoupper($key))] = $value;
            }
            unset($options['key']);
        }

        $options[\PDO::ATTR_ERRMODE] = \PDO::ERRMODE_EXCEPTION;

        /**
         * Check if the user has defined a custom dsn
         */
        if (isset($descriptor['dsn'])) {
            if (is_string($descriptor['dsn'])) {
                $dsnAttributes = $descriptor['dsn'];
            }
            unset($descriptor['dsn']);
        } else {
            $dsnParts = [];

            foreach ($descriptor as $key => $value) {
                $dsnParts[] = $key . '=' . $value;
            }

            $dsnAttributes = join(';', $dsnParts);
        }

        /**
         * Create the connection using PDO
         */
        $this->_pdo = new \PDO($this->getPdoDriver() . ':' . $dsnAttributes, $username, $password, $options);

        return true;
    }


    /**
     * Detect and return a PDO object.
     *
     * @param array $options
     * @return null|\PDO
     */
    protected function detectPdoObject(array $options)
    {
        foreach ($options as $option) {
            if (is_object($option) && $option instanceof \PDO) {
                return $option;
            }
        }

        return null;
    }

    public function isReady(): bool
    {
        return $this->_isConnected;
    }
}
