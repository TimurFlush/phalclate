<?php namespace Entity;

use TimurFlush\Phalclate\Entity\Dialect;

class DialectTest extends \Codeception\Test\Unit
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
        $this->tester->wantToTest('Object creating with invalid and valid dialects');

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
                $this->expectExceptionMessage('A passed dialect is not valid.');
            }

            (new Dialect($language));
        }
    }

    public function testGettingDialectViaUsualMethod()
    {
        $this->tester->wantToTest('Getting dialect via usual method.');

        $dialect = new Dialect('US');
        $this->assertEquals('US', $dialect->getDialect());

        $dialect = new Dialect('GB');
        $this->assertEquals('GB', $dialect->getDialect());
    }

    public function testGettingDialectViaMagicMethod()
    {
        $this->tester->wantToTest('Getting dialect via magic method.');

        $dialect = new Dialect('US');
        $this->assertEquals('US', strval($dialect));

        $dialect = new Dialect('GB');
        $this->assertEquals('GB', strval($dialect));
    }
}