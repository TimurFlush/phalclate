<?php
/*
 ***********************************************************************
 * Copyright (c) 2018 - present, Timur Flush. All rights reserved.
 ***********************************************************************
 * Author: Timur Flush <flush02@tutanota.com> <https://github.com/timurflush>
 ***********************************************************************
*/
namespace TimurFlush\Phalclate;

/**
 * Trait HelperTrait
 * @package TimurFlush\Phalclate
 */
trait HelperTrait
{
    /**
     * Is valid a translation key.
     *
     * @param   string $key
     * @return  bool
     */
    private function isValidTranslationKey($key)
    {
        return is_string($key) && preg_match('/^[\p{L}._0-9]{1,}$/', $key);
    }

    /**
     * Is valid language.
     *
     * @see https://en.wikipedia.org/wiki/ISO_639-1
     * @param   string $language
     * @return  bool
     */
    private function isValidLanguage($language)
    {
        return is_string($language) && preg_match('/^[a-z]{2}$/', $language);
    }

    /**
     * Is valid dialect.
     *
     * @param   string $dialect
     * @return  bool
     */
    private function isValidDialect($dialect)
    {
        return is_string($dialect) && preg_match('/^[A-Za-z_-]{1,}$/', $dialect);
    }
}
