<?php namespace Adapter\Pdo;

use Codeception\Util\Fixtures;
use TimurFlush\Phalclate\Adapter\Pdo\Postgresql;

class PostgresqlTesрt extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    
    protected function _before()
    {
        if (extension_loaded('pdo') === false) {
            $this->markTestSkipped('Pdo extensions is not loaded.');
        } elseif (extension_loaded('pdo_pgsql') === false) {
            $this->markTestSkipped('Pdo_pgsql extension is not loaded.');
        }
    }

    protected function _after()
    {

    }

    public function testGetPdoDriver()
    {
        $this->tester->wantToTest('Check the Pdo driver name.');

        $this->assertEquals(
            'pgsql',
            $this->getClient()->getPdoDriver(),

            'Pdo driver name is not pgsql.'
        );
    }

    public function getClient()
    {
        try {
            $client = new Postgresql(
                [
                    'host' => getenv('DB_PGSQL_HOST'),
                    'port' => getenv('DB_PGSQL_PORT'),
                    'dbname' => getenv('DB_PGSQL_DBNAME'),
                    'username' => getenv('DB_PGSQL_USERNAME'),
                    'password' => getenv('DB_PGSQL_PASSWORD'),
                    'schema' => getenv('DB_PGSQL_SCHEMA'),
                    'tableName' => getenv('DB_PGSQL_TABLE_NAME'),
                ]
            );

            $client->setBaseLanguages(Fixtures::get('languages'));
            $client->setCurrentLanguage(getenv('MODULE_CURRENT_LANGUAGE'));
            $client->setCurrentRegion(getenv('MODULE_CURRENT_DIALECT'));
            $client->setFailOverTranslation(getenv('MODULE_FAIL_OVER_TRANSLATION'));

            return $client;
        } catch (\Throwable $e) {
            $this->markTestSkipped($e->getMessage());
        }
    }
}