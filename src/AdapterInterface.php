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

/**
 * Interface AdapterInterface
 * @package TimurFlush\Phalclate
 */
interface AdapterInterface
{
    /**
     * Set the current language.
     * @param string $language
     */
    public function setCurrentLanguage(string $language);

    /**
     * Get the current language.
     * @return string
     */
    public function getCurrentLanguage(): string;

    /**
     * Set the current dialect.
     * @param string $dialect
     */
    public function setCurrentDialect(string $dialect);

    /**
     * Get the current dialect.
     * @return null|string
     */
    public function getCurrentDialect();

    /**
     * Set the fail-over translation.
     * @param string $text
     */
    public function setFailOverTranslation(string $text);

    /**
     * Get the fail-over translation.
     * @return string
     */
    public function getFailOverTranslation(): string;

    /**
     * Set the list of base languages.
     * @param Language[] $languages
     */
    public function setBaseLanguages(array $languages);

    /**
     * Get the list of base languages.
     * @return Language[]
     */
    public function getBaseLanguages(): array;

    /**
     * Get translation.
     * @param string $key Key
     * @param null|string $language Language.
     * @param null|string $dialect Dialect.
     * @param bool $firstFetch First fetch mode.
     * @return string|null
     */
    public function getTranslation(string $key, ?string $language, ?string $dialect, bool $firstFetch);

    /**
     * Set cache service.
     * @param \Phalcon\Cache\BackendInterface $cache
     */
    public function setCache(\Phalcon\Cache\BackendInterface $cache);

    /**
     * Get cache service.
     * @return null|\Phalcon\Cache\BackendInterface
     */
    public function getCache();

    /**
     * Delete a key from cache service.
     * @param string $key
     */
    public function cacheDeleteKey(string $key);

    /**
     * Get a translation by a passed key.
     * @param string $key A key.
     * @param mixed $args Other arguments.
     * @return mixed
     */
    public function _(string $key, ...$args);
}
