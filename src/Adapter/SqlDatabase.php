<?php
/*
 ***********************************************************************
 * Copyright (c) 2018 - present, Timur Flush. All rights reserved.
 ***********************************************************************
 * Author: Timur Flush <flush02@tutanota.com> <https://github.com/timurflush>
 ***********************************************************************
*/
namespace TimurFlush\Phalclate\Adapter;

use Phalcon\Db\AdapterInterface as DbAdapterInterface;
use TimurFlush\Phalclate\Adapter;

class SqlDatabase extends Adapter
{
    /**
     * @var DbAdapterInterface
     */
    protected $_db;

    public function __construct(array $options)
    {
        if (!isset($options['db'])) {
            throw new \Exception(
                'Instance of Phalcon\Db\AdapterInterface must be specified with the option \'db\''
            );
        } elseif ($options['db'] instanceof DbAdapterInterface === false) {
            throw new \Exception('The \'db\' option must be an instance of \Phalcon\Db\AdapterInterface');
        }

        $this->_db->getDialect()

        parent::__construct($options);
    }
}
