<?php

namespace Adapter\Pdo\Postgresql;

use TimurFlush\Phalclate\Tests\Unit\Traits\Pdo\Postgresql as PostgresqlTrait;

class checkPdoDriverNameTest extends \Codeception\Test\Unit
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

    public function testCheckPdoDriverName()
    {
        $this->tester->wantToTest('Check pdo driver name.');

        $adapter = $this->connectionViaCreatingPdoObject();

        $this->assertEquals('pgsql', $adapter->getPdoDriver());
    }
}