<?php namespace Entity;

use TimurFlush\Phalclate\Entity\Region;

class RegionTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    
    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function testObjectCreatingWithInvalidAndValidCountries()
    {
        $this->tester->wantToTest('Object creating with invalid and valid regions');

        $validMap = [
            'US' => false,
            'GB' => false,
            'RU' => false,
            'KZ' => false
        ];

        $invalidMap = [
            'someText' => true,
            'path/to/pornhub' => true,
            'IAmBitch' => true
        ];

        $map = array_merge($validMap, $invalidMap);

        foreach ($map as $language => $expectException) {
            if ($expectException) {
                $this->expectExceptionMessage('A passed region is not valid.');
            }

            (new Region($language));
        }
    }

    public function testGettingRegionViaUsualMethod()
    {
        $this->tester->wantToTest('Getting region via usual method.');

        $region = new Region('US');
        $this->assertEquals('US', $region->getRegion());

        $region = new Region('GB');
        $this->assertEquals('GB', $region->getRegion());
    }

    public function testGettingRegionViaMagicMethod()
    {
        $this->tester->wantToTest('Getting region via magic method.');

        $region = new Region('US');
        $this->assertEquals('US', strval($region));

        $region = new Region('GB');
        $this->assertEquals('GB', strval($region));
    }
}