<?php

namespace Adapter\Pdo\Postgresql;

use Codeception\Stub\Expected;
use TimurFlush\Phalclate\Tests\Unit\Traits\Pdo\Postgresql as PostgresqlTrait;

class connectionTest extends \Codeception\Test\Unit
{
    use PostgresqlTrait;

    /**
     * @var \UnitTester
     */
    protected $tester;

    public function _before()
    {
        if (extension_loaded('pdo') === false) {
            $this->markTestSkipped('pdo extension is not loaded.');
        }
        if (extension_loaded('pdo_pgsql') === false) {
            $this->markTestSkipped('pdo_pgsql extension is not loaded.');
        }
    }

    public function testConnectionViaCustomPdoObject()
    {
        $this->tester->wantToTest('Connection via custom Pdo object.');

        $adapter = $this->connectionViaCustomPdoObject();

        $this->assertTrue($adapter->isReady(), 'Problems with connection.');
    }

    public function testConnectionViaCreatingPdoObject()
    {
        $this->tester->wantToTest('Connection via creating Pdo object.');

        $adapter = $this->connectionViaCreatingPdoObject();

        $this->assertTrue($adapter->isReady(), 'Problems with connection.');
    }

    public function testConnectionViaCreatingPdoObjectWithCustomDsn()
    {
        $this->tester->wantToTest('Connection via creating Pdo object with custom dsn.');

        $adapter = $this->connectionViaCreatingPdoObject(false, true);

        $this->assertTrue($adapter->isReady(), 'Problems with connection.');
    }

    public function testThrowingExceptionWhenOptionTableNameIsNotExistst()
    {
        $this->tester->wantToTest('Throwing exception when option \'tableName\' is not exists.');

        $this->expectException(\Exception::class);
        $this->connectionViaCreatingPdoObject(true);
    }

    public function testThrowingExceptionWhenPassedPdoDriverIsNotValid()
    {
        $this->tester->wantToTest('Throwing exception when passed pdo driver is not valid.');

        $pdo = $this->make(
            \PDO::class,
            [
                'getAttribute' => Expected::atLeastOnce(
                    function ($attribute) {
                        if ($attribute !== \PDO::ATTR_DRIVER_NAME) {
                            throw new \Exception('Mock error.');
                        }

                        return 'someDriverName';
                    }
                )
            ]
        );

        $this->expectExceptionMessage('Passed PDO object is not pgsql driver.');
        $this->connectionViaCustomPdoObject($pdo);
    }
}