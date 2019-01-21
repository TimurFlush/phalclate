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

    public function getDialect(): string
    {
        return $this->_dialect;
    }

    public function __toString()
    {
        return $this->_dialect;
    }
}
