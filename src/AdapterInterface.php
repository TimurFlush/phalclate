<?php
/*
 ***********************************************************************
 * Copyright (c) 2018 - present, Timur Flush. All rights reserved.
 ***********************************************************************
 * Author: Timur Flush <flush02@tutanota.com> <https://github.com/timurflush>
 ***********************************************************************
*/
namespace TimurFlush\Phalclate;

interface AdapterInterface
{
    /**
     * Get the current language.
     *
     * @return string
     */
    public function getCurrentLanguage(): string;

    /**
     * Get the current dialect.
     *
     * @return null|string
     */
    public function getCurrentDialect();

    /**
     * Set the fail-over translation.
     *
     * @param string $tra
     * @return mixed
     */
    public function setFailOverTranslation(string $tra);

    /**
     * Get the fail-over translation.
     *
     * @return string
     */
    public function getFailOverTranslation(): string;

    /**
     * Restore the fail-over translation.
     *
     * @return void
     */
    public function restoreFailOverTranslation(): void;

    /**
     * Register a basic translations.
     *
     * @param array $source Source of a basic translations.
     * @return void
     */
    public function registerBasicTranslations(array $source);

    /**
     * Set the list of languages.
     *
     * @param array $languages The list of languages.
     * @return mixed
     */
    public function setLanguages(array $languages);

    /**
     * Get the list of languages.
     *
     * @return array
     */
    public function getLanguages(): array;

    /**
     * Get the list of translation groups.
     *
     * @return mixed
     */
    public function getTranslationGroups();

    /**
     * Get a translation by a passed key.
     *
     * @param string $key A key.
     * @param string $failOverTranslation A fail-over translation.
     * @return mixed
     */
    public function _(string $key, string $failOverTranslation = null);
}
