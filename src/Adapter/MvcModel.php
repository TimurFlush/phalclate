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
 * Class SqlDatabase
 * @package TimurFlush\Phalclate\Adapter
 */
class Phql extends Adapter implements AdapterInterface
{
    /**
     * @var \Phalcon\Mvc\Model\ManagerInterface
     */
    private $_modelsManager;

    /**
     * @var \Phalcon\Mvc\Model\MetaDataInterface
     */
    private $_modelsMetadata;

    /**
     * @var string Table name.
     */
    private $_tableName;

    /**
     * SqlDatabase constructor.
     *
     * @param array $options
     * @throws \Exception
     */
    public function __construct(array $options)
    {
        if (!isset($options['modelsManager'])) {
            throw new \Exception('The \'modelsManager\' option is not passed.');
        } elseif ($options['modelsManager'] instanceof \Phalcon\Mvc\Model\ManagerInterface === false) {
            throw new \Exception(
                'A passed modelsManager is not implement \Phalcon\Mvc\Model\ManagerInterface interface.'
            );
        }

        if (!isset($options['modelsMetadata'])) {
            throw new \Exception('The \'modelsMetadata\' option is not passed.');
        } elseif ($options['modelsMetadata'] instanceof \Phalcon\Mvc\Model\MetaDataInterface === false) {
            throw new \Exception(
                'A passed modelsMetadata is not implement \Phalcon\Mvc\Model\MetaDataInterface interface.'
            );
        }

        if (!isset($options['tableName'])) {
            throw new \Exception('The \'tableName\' option is not passed.');
        } elseif (!is_string($options['tableName']) && !empty($options['tableName'])) {
            throw new \Exception('A passed table name must be not empty string.');
        }

        $this->_modelsManager = $options['modelsManager'];
        $this->_modelsMetadata = $options['modelsMetadata'];
        $this->_tableName = $options['tableName'];

        parent::__construct($options);
    }

    public function getTranslation(string $key, ?string $language, ?string $dialect)
    {
        $this->_modelsManager->createQuery("SELECT * FROM [] ");
    }

    public function getTranslations(?string $language, ?string $dialect): array
    {

    }
}
