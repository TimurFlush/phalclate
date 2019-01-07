<?php
/*
 ***********************************************************************
 * Copyright (c) 2018 - present, Timur Flush. All rights reserved.
 ***********************************************************************
 * Author: Timur Flush <flush02@tutanota.com> <https://github.com/timurflush>
 ***********************************************************************
*/
namespace TimurFlush\Phalclate\Adapter;

use TimurFlush\Phalclate\Adapter;
use TimurFlush\Phalclate\AdapterInterface;

/**
 * Class MvcModel
 * @package TimurFlush\Phalclate\Adapter
 */
class MvcModel extends Adapter implements AdapterInterface
{
    /**
     * @var \Phalcon\Mvc\ModelInterface
     */
    private $_modelClass;

    /**
     * SqlDatabase constructor.
     *
     * @param array $options
     * @throws \Exception
     */
    public function __construct(array $options)
    {
        if (!isset($options['modelClass'])) {
            throw new \Exception('The \'modelClass\' option is not passed.');
        } elseif (!class_exists($options['modelClass'])) {
            throw new \Exception('A passed model class is not exists.');
        } elseif ($options['modelClass'] instanceof \Phalcon\Mvc\ModelInterface === false) {
            throw new \Exception('A passed model is not implement \Phalcon\Mvc\ModelInterface interface.');
        }

        $this->_modelClass = $options['modelClass'];

        parent::__construct($options);
    }

    /**
     * Get translation.
     * @param string $key Key
     * @param null|string $language Language.
     * @param null|string $dialect Dialect.
     * @param bool $firstFetch First fetch mode.
     * @return string|null
     */
    public function getTranslation(string $key, ?string $language, ?string $dialect, bool $firstFetch)
    {
        $language = $language ?? $this->getCurrentLanguage();
        $dialect = $dialect ?? $this->getCurrentDialect();

        $translationRecord = $this->_modelClass::findFirst(
            [
                '[key] = :key: AND [language] = :language:',
                'bind' => [
                    'key' => $key,
                    'language' => $language,
                ]
            ]
        );

        if ($translationRecord) {
            if ($translationRecord->dialect === $dialect) {
                return $translationRecord;
            } elseif ($translationRecord->dialect !== $dialect && $firstFetch === true) {
                return $translationRecord->value;
            }
        }

        return null;
    }
}
