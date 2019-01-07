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
 * Class Language
 * @package TimurFlush\Phalclate\Entity
 */
class Language
{
    use HelperTrait;

    /**
     * @var string
     */
    private $_language;

    /**
     * @var Dialect[]
     */
    private $_dialects;

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
     *
     * @param string $language
     * @return Language
     * @throws \Exception
     */
    public static function create(string $language)
    {
        return new self($language);
    }

    /**
     * Set dialect.
     *
     * @param Dialect $dialect
     * @return void
     */
    public function setDialect(Dialect $dialect): void
    {
        $this->_dialects[] = $dialect;
    }

    /**
     * Set dialects.
     *
     * @param array $dialects
     * @throws \Exception One from passed dialect is not extends TimurFlush\Phalclate\Entity\Dialect class.
     * @return void
     */
    public function setDialects(array $dialects): void
    {
        foreach ($dialects as $dialect) {
            if ($dialect instanceof Dialect === false) {
                throw new \Exception(
                    'One from passed dialects is not extends TimurFlush\Phalclate\Entity\Dialect class.'
                );
            }

            $this->setDialect($dialect);
        }
    }

    /**
     * Get dialects.
     *
     * @return Dialect[]
     */
    public function getDialects(): array
    {
        return $this->_dialects;
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
