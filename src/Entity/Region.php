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

class Region
{
    use HelperTrait;

    /**
     * @var string
     */
    private $_region;

    /**
     * Region constructor.
     *
     * @param string $region
     * @throws \Exception A passed region is not valid.
     */
    public function __construct(string $region)
    {
        if (!$this->isValidRegion($region)) {
            throw new \Exception('A passed region is not valid.');
        }

        $this->_region = $region;
    }

    public function getRegion(): string
    {
        return $this->_region;
    }

    public function __toString()
    {
        return $this->_region;
    }
}
