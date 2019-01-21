<?php namespace Entity;

use Codeception\Stub\Expected;
use TimurFlush\Phalclate\Entity\Dialect;
use TimurFlush\Phalclate\Entity\Language;

class LanguageTest extends \Codeception\Test\Unit
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
        $this->tester->wantToTest('Object creating with invalid and valid countries');

        $validMap = [
            'ru' => false,
            'en' => false,
            'uk' => false,
            'ua' => false
        ];

        $invalidMap = [
            'someText' => true,
            'path/to/pornhub' => true,
            'IAmBitch' => true
        ];

        $map = array_merge($validMap, $invalidMap);

        foreach ($map as $language => $expectException) {
            if ($expectException) {
                $this->expectExceptionMessage('A passed language is not valid.');
            }

            (new Language($language));
        }
    }

    public function testGettingLanguageViaUsualMethod()
    {
        $this->tester->wantToTest('Getting language via usual method.');

        $language = new Language('ru');
        $this->assertEquals('ru', $language->getLanguage());

        $language = new Language('en');
        $this->assertEquals('en', $language->getLanguage());
    }

    public function testGettingLanguageViaMagicMethod()
    {
        $this->tester->wantToTest('Getting language via magic method.');

        $language = new Language('ru');
        $this->assertEquals('ru', strval($language));

        $language = new Language('en');
        $this->assertEquals('en', strval($language));
    }

    public function testAccessorsForDialectsProperty()
    {
        $this->tester->wantToTest('Accessors for dialects property.');

        $usDialect = $this->make(
            Dialect::class,
            [
                'getDialect' => Expected::atLeastOnce(function () {
                    return 'US';
                })
            ]
        );
        $gbDialect = $this->make(
            Dialect::class,
            [
                'getDialect' => Expected::atLeastOnce(function () {
                    return 'GB';
                })
            ]
        );

        $language = new Language('en');
        $this->assertEmpty($language->getDialects());

        $language->setDialects(
            [
                $usDialect,
                $gbDialect
            ]
        );

        foreach ($language->getDialects() as $key => $dialect) {
            switch ($key) {
                case 'US':
                    $expected = 'US';
                    break;
                case 'GB':
                    $expected = 'GB';
                    break;
                default:
                    $expected = '0_o';
            }
            $this->assertEquals($expected, $dialect->getDialect());
        }
    }
}