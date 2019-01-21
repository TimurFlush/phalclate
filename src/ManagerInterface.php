<?php
/*
 ***********************************************************************
 * Copyright (c) 2018 - present, Timur Flush. All rights reserved.
 ***********************************************************************
 * Author: Timur Flush <flush02@tutanota.com> <https://github.com/timurflush>
 ***********************************************************************
*/
namespace TimurFlush\Phalclate;

use TimurFlush\Phalclate\Entity\Language;

interface ManagerInterface
{
    /**
     * Get a translation by a passed key.
     *
     * @param   string  $key        A key.
     * @param   mixed   $arguments  Other arguments.
     * @return  mixed
     */
    public function getTranslation(string $key, ...$arguments);

    /**
     * Set base languages.
     *
     * @param Language[] $languages
     */
    public function setBaseLanguages(array $languages);

    /**
     * Get base languages.
     *
     * @return Language[]
     */
    public function getBaseLanguages(): array;

    /**
     * Set current language.
     *
     * @param string $language
     */
    public function setCurrentLanguage(string $language);

    /**
     * Get current language.
     *
     * @return null|string
     */
    public function getCurrentLanguage();

    /**
     * Set current dialect.
     *
     * @param string $dialect
     */
    public function setCurrentDialect(string $dialect);

    /**
     * Get current dialect.
     *
     * @return null|string
     */
    public function getCurrentDialect();

    /**
     * Set fail over translation.
     *
     * @param string $translation
     */
    public function setFailOverTranslation(string $translation);

    /**
     * Get fail over translation.
     *
     * @return string
     */
    public function getFailOverTranslation(): string;

    /**
     * Set cache object.
     *
     * @param \Phalcon\Cache\BackendInterface $cache
     */
    public function setCache(\Phalcon\Cache\BackendInterface $cache);

    /**
     * Get cache object.
     *
     * @return null|\Phalcon\Cache\BackendInterface
     */
    public function getCache();
}
