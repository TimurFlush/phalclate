<?php
/*
 ***********************************************************************
 * Copyright (c) 2018 - present, Timur Flush. All rights reserved.
 ***********************************************************************
 * Author: Timur Flush <flush02@tutanota.com> <https://github.com/timurflush>
 ***********************************************************************
*/
namespace TimurFlush\Phalclate\Adapter\Pdo;

use TimurFlush\Phalclate\Adapter\Pdo;

class Postgresql extends Pdo
{
    /**
     * Postgresql constructor.
     * @param array $options
     * @throws \Exception The PDO/pgsql extension is not loaded.
     */
    public function __construct(array $options)
    {
        parent::__construct($options);
    }

    /**
     * Get PDO driver.
     *
     * @return string
     */
    public function getPdoDriver(): string
    {
        return 'pgsql';
    }

    /**
     * Connect to database.
     *
     * @see https://github.com/phalcon/cphalcon/blob/master/phalcon/db/adapter/pdo/postgresql.zep#L52
     * @param array $descriptor
     * @return bool
     */
    protected function connect(array $descriptor)
    {
        $schema = 'public';

        if (isset($descriptor['schema'])) {
            if (is_string($descriptor['schema'])) {
                $schema = $descriptor['schema'];
            }
            unset($descriptor['schema']);
        }

        $status = parent::connect($descriptor);

        if ($status) {
            $this->_pdo->exec("SET search_path TO '" . $schema . "'");
        }

        return $status;
    }

    public function getTranslation(string $key, string $language, ?string $region = null, bool $firstFetch = false)
    {
        $query = $this->_pdo
            ->prepare(
                'SELECT * FROM "' . $this->_tableName . '" WHERE "key" = ? AND "language" = ?'
            );

        $query->execute(
            [
                $key,
                $language
            ]
        );

        $translations = $query->fetchAll(\PDO::FETCH_ASSOC);

        if (is_array($translations)) {
            foreach ($translations as $translation) {
                if ($translation['region'] === $region) {
                    return $translation['value'];
                }
            }

            if (isset($translations[0]['value']) && $firstFetch) {
                return $translations[0]['value'];
            }
        }

        return null;
    }
}
