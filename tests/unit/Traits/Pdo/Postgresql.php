<?php

namespace TimurFlush\Phalclate\Tests\Unit\Traits\Pdo;

use TimurFlush\Phalclate\Adapter\Pdo\Postgresql as PostgresqlAdapter;

trait Postgresql
{
    public function connectionViaCreatingPdoObject(bool $withoutTableName = false, bool $asDsn = false)
    {
        $options = [
            'username' => getenv('DB_PGSQL_USERNAME'),
            'password' => getenv('DB_PGSQL_PASSWORD'),
            'schema' => getenv('DB_PGSQL_SCHEMA'),
            'options' => [
                'ATTR_ERRMODE' => \PDO::ERRMODE_EXCEPTION
            ],
        ];

        if ($asDsn) {
            $options = array_merge($options, [
                'dsn' => sprintf(
                    'host=%s;port=%s;dbname=%s;',
                    getenv('DB_PGSQL_HOST'),
                    getenv('DB_PGSQL_PORT'),
                    getenv('DB_PGSQL_DBNAME')
                )
            ]);
        } else {
            $options = array_merge($options, [
                'host' => getenv('DB_PGSQL_HOST'),
                'port' => getenv('DB_PGSQL_PORT'),
                'dbname' => getenv('DB_PGSQL_DBNAME'),
            ]);
        }

        if ($withoutTableName === false) {
            $options['tableName'] = getenv('DB_PGSQL_TABLE_NAME');
        }

        return new PostgresqlAdapter($options);
    }

    public function connectionViaCustomPdoObject(\PDO $pdo = null, bool $withoutTableName = false)
    {
        if ($pdo === null) {
            $dsn = sprintf(
                'pgsql:host=%s;port=%s;dbname=%s;',
                getenv('DB_PGSQL_HOST'),
                getenv('DB_PGSQL_PORT'),
                getenv('DB_PGSQL_DBNAME')
            );

            $pdo = new \PDO(
                $dsn,
                getenv('DB_PGSQL_USERNAME'),
                getenv('DB_PGSQL_PASSWORD')
            );
        }

        $options = [
            $pdo
        ];

        if ($withoutTableName === false) {
            $options['tableName'] = getenv('DB_PGSQL_TABLE_NAME');
        }

        return new PostgresqlAdapter($options);
    }
}
