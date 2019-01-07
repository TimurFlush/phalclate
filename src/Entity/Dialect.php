<?php
/*
 ***********************************************************************
 * Copyright (c) 2018 - present, Timur Flush. All rights reserved.
 ***********************************************************************
 * Author: Timur Flush <flush02@tutanota.com> <https://github.com/timurflush>
 ***********************************************************************
*/
namespace TimurFlush\Phalclate\Entity;

use TimurFlush\Phalclate\HelperTrait;

/**
 * Class Dialect
 * @package TimurFlush\Phalclate\Entity
 */
class Dialect
{
    use HelperTrait;

    /**
     * @var string
     */
    private $_dialect;

    /**
     * Dialect constructor.
     *
     * @param string $dialect
     * @throws \Exception A passed dialect is not valid.
     */
    public function __construct(string $dialect)
    {
        if (!$this->isValidDialect($dialect)) {
            throw new \Exception('A passed dialect is not valid.');
        }

        $this->_dialect = $dialect;
    }

    /**
     * Create a dialect.
     *
     * @param string $dialect
     * @return Dialect
     * @throws \Exception
     */
    public static function create(string $dialect)
    {
        return new self($dialect);
    }

    /**
     * Create an array of objects of class Dialect.
     *
     * @param string[] $dialects
     * @throws \Exception
     * @return Dialect[]
     */
    public static function createFromArray(array $dialects)
    {
        $pool = [];

        foreach ($dialects as $dialect) {
            $pool[] = self::create($dialect);
        }

        return $pool;
    }

    public function getDialect(): string
    {
        return $this->_dialect;
    }

    public function __toString()
    {
        return $this->_dialect;
    }
}
