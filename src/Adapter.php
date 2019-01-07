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
 * Class Adapter
 * @package TimurFlush\Phalclate
 */
abstract class Adapter implements AdapterInterface
{
    use HelperTrait;

    /**
     * @var string Current language.
     */
    protected $_currentLanguage;

    /**
     * @var string|null Current dialect.
     */
    protected $_currentDialect;

    /**
     * @var string Fail-over translation.
     */
    protected $_failOverTranslation = '?-?';

    /**
     * @var Language[] Array of objects of class Language.
     */
    protected $_baseLanguages = [];

    /**
     * @var \Phalcon\Cache\BackendInterface Cache service.
     */
    protected $_cache;

    /**
     * Adapter constructor.
     * @param array $options
     * @throws \Exception
     */
    public function __construct(array $options = [])
    {
        if (isset($options['baseLanguages'])) {
            $this->setBaseLanguages($options['baseLanguages']);
        }
        if (isset($options['currentLanguage'])) {
            $this->setCurrentLanguage($options['currentLanguage']);
        }
        if (isset($options['currentDialect'])) {
            $this->setCurrentDialect($options['currentDialect']);
        }
        if (isset($options['failOverTranslation'])) {
            $this->setFailOverTranslation($options['failOverTranslation']);
        }
        if (isset($options['cache'])) {
            $this->setCache($options['cache']);
        }
    }

    /**
     * Set the list of base languages.
     * @param Language[] $languages
     */
    public function setBaseLanguages(array $languages)
    {
        foreach ($languages as $language) {
            $this->_baseLanguages[$language->getLanguage()] = $language;
        }
    }

    /**
     * Get the list of base languages.
     * @return Language[]
     */
    public function getBaseLanguages(): array
    {
        return $this->_baseLanguages;
    }

    /**
     * Set the current language.
     * @param string $language
     * @return mixed|void
     * @throws \Exception A language must be 2 characters long.
     * @throws \Exception You cannot set the current language until it is not the base language.
     */
    public function setCurrentLanguage(string $language)
    {
        if (!$this->isValidLanguage($language)) {
            throw new \Exception('A language must be 2 characters long.');
        } elseif (!array_key_exists($language, $this->_baseLanguages)) {
            throw new \Exception('You cannot set the current language until it is not the base language.');
        }

        $this->_currentLanguage = $language;
    }

    /**
     * Get the current language.
     * @return string
     */
    public function getCurrentLanguage(): string
    {
        return $this->_currentLanguage;
    }

    /**
     * Set the current dialect.
     * @param string $dialect
     * @throws \Exception A dialect is not valid.
     * @throws \Exception You cannot set the dialect until the language is not set.
     */
    public function setCurrentDialect(string $dialect)
    {
        if (!$this->isValidDialect($dialect)) {
            throw new \Exception('A dialect is not valid.');
        } elseif (empty($this->_currentLanguage)) {
            throw new \Exception('You cannot set the dialect until the language is not set.');
        }

        if (in_array($dialect, $this->_baseLanguages[$this->_currentLanguage]->getDialects())) {
            $this->_currentDialect = $dialect;
        }
    }

    /**
     * Get the current dialect.
     * @return null|string
     */
    public function getCurrentDialect()
    {
        return $this->_currentDialect;
    }

    /**
     * Set the fail-over translation.
     * @param string $text
     */
    public function setFailOverTranslation(string $text)
    {
        $this->_failOverTranslation = $text;
    }

    /**
     * Get the fail-over translation.
     * @return string
     */
    public function getFailOverTranslation(): string
    {
        return $this->_failOverTranslation;
    }

    /**
     * Set cache service.
     * @param \Phalcon\Cache\BackendInterface $cache
     */
    public function setCache(\Phalcon\Cache\BackendInterface $cache)
    {
        $this->_cache = $cache;
    }

    /**
     * Get cache service.
     * @return null|\Phalcon\Cache\BackendInterface
     */
    public function getCache()
    {
        return $this->_cache;
    }

    /**
     * Delete a key from cache service.
     * @param string $key
     */
    public function cacheDeleteKey(string $key)
    {
        if ($this->_cache !== null && $this->_cache->exists($key)) {
            $this->_cache->delete($key);
        }
    }

    /**
     *
     * @param string $key Translation key.
     * @param mixed ...$args Other arguments.
     * @return string
     * @throws \Exception Passed the invalid translation key: <key>.
     */
    public function _(string $key, ...$args)
    {
        if (!$this->isValidTranslationKey($key)) {
            throw new \Exception('Passed the invalid translation key: ' . $key);
        }

        $failOverTranslation = null;
        $placeholders = null;
        $firstFetch = false;

        $argsNum = sizeof($args);

        if ($argsNum > 0) {
            for ($i = 0; $i <= $argsNum; $i++) {
                $arg = &$args[$i];
                if (is_string($arg)) {
                    $failOverTranslation = $arg;
                } elseif (is_array($arg)) {
                    $placeholders = $arg;
                } elseif (is_bool($arg)) {
                    $firstFetch = $arg;
                }
            }
        }

        if (empty($key)) {
            return $failOverTranslation ?? $this->_failOverTranslation;
        }

        if ($this->_cache === null) {
            $translation = $this->getTranslation($key, $this->_currentLanguage, $this->_currentDialect, $firstFetch);
        } else {
            $cacheKeyName = sprintf('%s_%s_%s', $key, $this->_currentLanguage, $this->_currentDialect ?? '-');
            $cachedTranslation = $this->_cache->get($cacheKeyName);

            if ($cachedTranslation === null) {
                if ($translation = $this->getTranslation(
                    $key,
                    $this->_currentLanguage,
                    $this->_currentDialect,
                    $firstFetch) !== null
                ) {
                    $this->_cache->save($cacheKeyName, $translation);
                }
            } else {
                $translation = &$cachedTranslation;
            }
        }

        if ($translation === null) {
            return $failOverTranslation ?? $this->_failOverTranslation;
        } elseif ($translation === "") {
            return $translation;
        } elseif (is_array($placeholders) && !empty($placeholders)) {
            return str_replace(array_keys($placeholders), array_values($placeholders), $translation);
        } else {
            return $translation;
        }
    }
}
