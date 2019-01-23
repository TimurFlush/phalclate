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

class Language
{
    use HelperTrait;

    /**
     * @var string
     */
    private $_language;

    /**
     * @var Region[]
     */
    private $_regions = [];

    /**
     * Language constructor.
     *
     * @param string $language Language.
     * @throws \Exception Passed language is not valid.
     */
    public function __construct(string $language)
    {
        if (!$this->isValidLanguage($language)) {
            throw new \Exception('A passed language is not valid.');
        }

        $this->_language = $language;
    }

    /**
     * Add region.
     *
     * @param Region $region
     * @return void
     */
    public function addRegion(Region $region): void
    {
        $this->_regions[$region->getRegion()] = $region;
    }

    /**
     * Set regions.
     *
     * @param array $regions
     * @throws \Exception One from passed region is not extends TimurFlush\Phalclate\Entity\Region class.
     * @return void
     */
    public function setRegions(array $regions): void
    {
        foreach ($regions as $region) {
            $this->addRegion($region);
        }
    }

    /**
     * Get regions.
     *
     * @return Region[]
     */
    public function getRegions(): array
    {
        return $this->_regions;
    }

    /**
     * Get language.
     *
     * @return string
     */
    public function getLanguage(): string
    {
        return $this->_language;
    }

    /**
     *
     * @return string
     */
    public function __toString()
    {
        return $this->_language;
    }
}
