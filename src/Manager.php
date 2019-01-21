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

class Manager implements ManagerInterface
{
    use HelperTrait;

    protected $_adapter;

    /**
     * @var Language[]
     */
    protected $_baseLanguages = [];

    /**
     * @var null|string
     */
    protected $_currentLanguage;

    /**
     * @var null|string
     */
    protected $_currentDialect;

    /**
     * @var string
     */
    protected $_failOverTranslation = '[???]';

    /**
     * @var null|\Phalcon\Cache\BackendInterface
     */
    protected $_cache;

    /**
     * Manager constructor.
     *
     * @param   AdapterInterface    $adapter
     * @param   array               $options
     * @throws  \Exception
     */
    public function __construct(AdapterInterface $adapter, array $options)
    {
        if ($adapter->isReady() === false) {
            throw new \Exception('Adapter is not ready for work.');
        }

        if (isset($options['baseLanguages']) && sizeof($options['baseLanguages']) > 0) {
            $this->setBaseLanguages($options['baseLanguages']);
        } else {
            throw new \Exception('Parameter \'baseLanguages\' is not passed.');
        }

        if (isset($options['currentLanguage'])) {
            $this->setCurrentLanguage($options['currentLanguage']);
        } else {
            throw new \Exception('Parameter \'currentLanguage\' is not passed.');
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

        $this->_adapter = $adapter;
    }

    /**
     * Set base languages.
     *
     * @param   Language[]  $languages
     * @throws \Exception   A value of element of the array must be Phalclate\Entity\Language.
     */
    public function setBaseLanguages(array $languages)
    {
        foreach ($languages as $language) {
            if ($language instanceof Language === false) {
                throw new \Exception('A value of element of the array must be ' . Language::class);
            }
            $this->_baseLanguages[$language->getLanguage()] = $language;
        }
    }

    /**
     * Get base languages.
     *
     * @return Language[]
     */
    public function getBaseLanguages(): array
    {
        return $this->_baseLanguages;
    }

    /**
     * Set current language.
     *
     * @param   string      $language
     * @return  mixed|void
     * @throws  \Exception  A language must be 2 characters long.
     * @throws  \Exception  You cannot set the current language until it is not the base language.
     */
    public function setCurrentLanguage(string $language)
    {
        if (!$this->isValidLanguage($language)) {
            throw new \Exception('Passed language is not valid.');
        } elseif (!array_key_exists($language, $this->_baseLanguages)) {
            throw new \Exception('You cannot set the current language until it is not the base languages.');
        }

        $this->_currentLanguage = $language;
    }

    /**
     * Get the current language.
     *
     * @return null|string
     */
    public function getCurrentLanguage()
    {
        return $this->_currentLanguage;
    }

    /**
     * Set current dialect.
     *
     * @param   string      $dialect
     * @throws  \Exception  A dialect is not valid.
     * @throws  \Exception  You cannot specify a dialect if it is not defined in the current language.
     */
    public function setCurrentDialect(string $dialect)
    {
        if (!$this->isValidDialect($dialect)) {
            throw new \Exception('Passed dialect is not valid.');
        } elseif (empty($this->_currentLanguage)) {
            throw new \Exception('You cannot specify a dialect until the current language is not set.');
        }

        if (array_key_exists($dialect, $this->_baseLanguages[$this->_currentLanguage]->getDialects())) {
            $this->_currentDialect = $dialect;
        }
    }

    /**
     * Get current dialect.
     *
     * @return null|string
     */
    public function getCurrentDialect()
    {
        return $this->_currentDialect;
    }

    /**
     * Set fail over translation.
     *
     * @param string $translation
     */
    public function setFailOverTranslation(string $translation)
    {
        $this->_failOverTranslation = $translation;
    }

    /**
     * Get fail over translation.
     *
     * @return string
     */
    public function getFailOverTranslation(): string
    {
        return $this->_failOverTranslation;
    }

    /**
     * Set cache object.
     *
     * @param \Phalcon\Cache\BackendInterface $cache
     */
    public function setCache(\Phalcon\Cache\BackendInterface $cache)
    {
        $this->_cache = $cache;
    }

    /**
     * Get cache object.
     *
     * @return null|\Phalcon\Cache\BackendInterface
     */
    public function getCache()
    {
        return $this->_cache;
    }

    /**
     * Get a translation by a passed key.
     *
     * @param   string  $key        A key.
     * @param   mixed   $arguments  Other arguments.
     * @return  mixed
     * @throws  \Exception          Passed the invalid translation key: %key%.
     */
    public function getTranslation(string $key, ...$arguments)
    {
        if ($this->isValidTranslationKey($key) === false) {
            throw new \Exception('Passed the invalid translation key: ' . $key);
        }

        $failOverTranslation = $placeholders = null;
        $firstFetchMode = false;

        foreach ($arguments as $argument) {
            if (is_string($argument)) {
                $failOverTranslation = $argument;
            } elseif (is_array($argument)) {
                $placeholders = $argument;
            } elseif (is_bool($argument)) {
                $firstFetchMode = $argument;
            }
        }

        if ($this->_cache === null) {
            $translation = $this->_adapter->getTranslation(
                $key,
                $this->_currentLanguage,
                $this->_currentDialect,
                $firstFetchMode
            );
        } else {
            $cacheKeyName = sprintf(
                '%s_%s_%s',
                $key,
                $this->_currentLanguage,
                $this->_currentDialect ?? '-'
            );

            $translation = $this->_cache->get($cacheKeyName);

            if ($translation === null) {
                if (($translation = $this->_adapter->getTranslation(
                    $key,
                    $this->_currentLanguage,
                    $this->_currentDialect,
                    $firstFetchMode)
                    ) !== null
                ) {
                    $this->_cache->save($cacheKeyName, $translation);
                }
            }
        }

        if ($translation === null) {
            return $failOverTranslation ?? $this->_failOverTranslation;
        } elseif (is_array($placeholders) && sizeof($placeholders) > 0) {
            $find = array_keys($placeholders);

            array_walk($find, function (&$value) {
                $value = '%' . $value . '%';
            });

            return str_replace(
                $find,
                array_values($placeholders),
                $translation
            );
        } else {
            return $translation;
        }
    }
}
