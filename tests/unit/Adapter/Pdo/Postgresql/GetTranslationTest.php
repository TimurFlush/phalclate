<?php

namespace Adapter\Pdo\Postgresql;

use Codeception\Util\Fixtures;
use TimurFlush\Phalclate\AdapterInterface;
use TimurFlush\Phalclate\Tests\unit\Traits\Pdo\Postgresql as PostgresqlTrait;

class getTranslationTest extends \Codeception\Test\Unit
{
    use PostgresqlTrait;

    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var AdapterInterface
     */
    protected $_adapter;

    public function _before()
    {
        foreach (Fixtures::get('translations') as $translation) {
            $this->tester->haveInDatabase(
                getenv('DB_PGSQL_TABLE_NAME'),
                $translation
            );
        }

        $adapter = $this->connectionViaCreatingPdoObject();

        $this->_adapter = $adapter;
    }

    public function _after()
    {
        $this->_adapter = null;
    }

    public function testGettingATranslationWithoutRegionAndWithFirstFetchMode()
    {
        $this->tester->wantToTest('Getting a translation without region and with first fetch mode.');

        $case1 = $this->_adapter->getTranslation('color', 'en', null, true);
        $case2 = $this->_adapter->getTranslation('position', 'en', null, true);

        $this->assertContains($case1, ['color', 'colour']);
        $this->assertContains($case2, ['center', 'centre']);
    }

    public function testGettingATranslationWithoutRegionAndWithoutFirstFetchMode()
    {
        $this->tester->wantToTest('Getting a translation without region and without first fetch mode.');

        $case1 = $this->_adapter->getTranslation('color', 'en', null, false);
        $case2 = $this->_adapter->getTranslation('position', 'en', null, false);

        $this->assertNull($case1);
        $this->assertNull($case2);
    }

    public function testGettingATranslationWithRegionAndWithFirstFetchMode()
    {
        $this->tester->wantToTest('Getting a translation with region and with first fetch mode.');

        $case1 = $this->_adapter->getTranslation('color', 'en', 'US', true);
        $case2 = $this->_adapter->getTranslation('color', 'en', 'GB', true);

        $this->assertEquals('color', $case1);
        $this->assertEquals('colour', $case2);

        $case1 = $this->_adapter->getTranslation('position', 'en', 'US', true);
        $case2 = $this->_adapter->getTranslation('position', 'en', 'GB', true);

        $this->assertEquals('center', $case1);
        $this->assertEquals('centre', $case2);
    }

    public function testGettingATranslationWithRegionAndWithoutFirstFetchMode()
    {
        $this->tester->wantToTest('Getting a translation with region and without first fetch mode.');

        $case1 = $this->_adapter->getTranslation('color', 'en', 'US', false);
        $case2 = $this->_adapter->getTranslation('color', 'en', 'GB', false);

        $this->assertEquals('color', $case1);
        $this->assertEquals('colour', $case2);

        $case1 = $this->_adapter->getTranslation('position', 'en', 'US', false);
        $case2 = $this->_adapter->getTranslation('position', 'en', 'GB', false);

        $this->assertEquals('center', $case1);
        $this->assertEquals('centre', $case2);
    }
}