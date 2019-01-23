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
     * Is valid region.
     *
     * @see https://tools.ietf.org/html/rfc5646#section-2.2.4
     * @param   string $region
     * @return  bool
     */
    private function isValidRegion($region)
    {
        return is_string($region) && preg_match('/^([A-Z]{2}|[0-9]{3})$/', $region);
    }
}
