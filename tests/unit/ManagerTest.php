<?php 
class ManagerTest extends \Codeception\Test\Unit
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

    public function testThrowingExceptionInConstructWhenAdapterIsNotReadyForWork()
    {
        $this->tester->wantToTest('Throwing exception in construct when adapter is not ready for work.');

        $adapter = $this->makeEmpty(
            \TimurFlush\Phalclate\AdapterInterface::class,
            [
                'isReady' => false
            ]
        );

        $this->expectExceptionMessage('Adapter is not ready for work.');
        (new \TimurFlush\Phalclate\Manager(
            $adapter,
            []
        ));
    }

    public function testThrowingExceptionWhenPassedNotLanguageObjectInSetterForBaseLanguagesProperty()
    {
        $this->tester->wantToTest(
            'Throwing exception when passed not language object in setter for base languages property'
        );

        $reflector = new ReflectionClass(\TimurFlush\Phalclate\Manager::class);

        /**
         * @var $adapter \TimurFlush\Phalclate\Manager
         */
        $adapter = $reflector->newInstanceWithoutConstructor();

        $this->expectExceptionMessageRegExp('/A value of element of the array must be/');
        $adapter->setBaseLanguages(
            [
                'BRAKE'
            ]
        );
    }

    /**
     * @depends testThrowingExceptionWhenPassedNotLanguageObjectInSetterForBaseLanguagesProperty
     */
    public function testAccessorsForBaseLanguagesProperty()
    {
        $this->tester->wantToTest('Accessors for \'_baseLanguages\' property.');

        $reflector = new ReflectionClass(\TimurFlush\Phalclate\Manager::class);

        /**
         * @var $adapter \TimurFlush\Phalclate\Manager
         */
        $adapter = $reflector->newInstanceWithoutConstructor();

        $this->assertEmpty($adapter->getBaseLanguages());

        $adapter->setBaseLanguages(
            [
                0 => $this->makeEmpty(\TimurFlush\Phalclate\Entity\Language::class, [
                    'getLanguage' => 'en'
                ]),
                1 => $this->makeEmpty(\TimurFlush\Phalclate\Entity\Language::class, [
                    'getLanguage' => 'ru'
                ])
            ]
        );

        foreach ($adapter->getBaseLanguages() as $key => $language) {
            switch ($key) {
                case 'en':
                    $this->assertEquals('en', $language->getLanguage());
                    break;
                case 'ru':
                    $this->assertEquals('ru', $language->getLanguage());
                    break;
                default:
                    $this->assertFalse(true);
            }
        }
    }

    /**
     * @depends testAccessorsForBaseLanguagesProperty
     */
    public function testThrowingExceptionInConstructWhenParameterBaseLanguagesIsNotPassed()
    {
        $this->tester->wantToTest('Throwing exception in construct when parameter \'baseLanguages\' is not passed.');

        $adapter = $this->makeEmpty(
            \TimurFlush\Phalclate\AdapterInterface::class,
            [
                'isReady' => true
            ]
        );

        $this->expectExceptionMessage('Parameter \'baseLanguages\' is not passed.');
        (new \TimurFlush\Phalclate\Manager(
            $adapter,
            []
        ));
    }

    /**
     * @depends testAccessorsForBaseLanguagesProperty
     */
    public function testThrowingExceptionWhenPassedLanguageIsNotValidInSetterForCurrentLanguageProperty()
    {
        $this->tester->wantToTest(
            'Throwing exception when passed language is not valid in setter for \'currentLanguage\' property.'
        );

        $reflector = new ReflectionClass(\TimurFlush\Phalclate\Manager::class);

        /**
         * @var $adapter \TimurFlush\Phalclate\Manager
         */
        $adapter = $reflector->newInstanceWithoutConstructor();

        $this->expectExceptionMessage('Passed language is not valid.');
        $adapter->setCurrentLanguage('BRAKE');
    }

    /**
     * @depends testAccessorsForBaseLanguagesProperty
     */
    public function testThrowingExceptionWhenPassedLanguageHaveNotInBaseLanguagesInSetterForCurrentLanguageProperty()
    {
        $this->tester->wantToTest(
            'Throwing exception when passed language have not in base languages in setter for \'currentLanguage\' property.'
        );

        $reflector = new ReflectionClass(\TimurFlush\Phalclate\Manager::class);

        /**
         * @var $adapter \TimurFlush\Phalclate\Manager
         */
        $adapter = $reflector->newInstanceWithoutConstructor();

        $adapter->setBaseLanguages(
            [
                $this->makeEmpty(\TimurFlush\Phalclate\Entity\Language::class, [
                    'getLanguage' => 'en'
                ])
            ]
        );

        $this->expectExceptionMessage('You cannot set the current language until it is not the base languages.');
        $adapter->setCurrentLanguage('ru');
    }

    /**
     * @depends testThrowingExceptionWhenPassedLanguageIsNotValidInSetterForCurrentLanguageProperty
     * @depends testThrowingExceptionWhenPassedLanguageHaveNotInBaseLanguagesInSetterForCurrentLanguageProperty
     */
    public function testAccessorsForCurrentLanguageProperty()
    {
        $this->tester->wantToTest('Accessors for \'_currentLanguage\' property.');

        $reflector = new ReflectionClass(\TimurFlush\Phalclate\Manager::class);

        /**
         * @var $adapter \TimurFlush\Phalclate\Manager
         */
        $adapter = $reflector->newInstanceWithoutConstructor();

        $this->assertNull($adapter->getCurrentLanguage());

        $adapter->setBaseLanguages(
            [
                $this->makeEmpty(\TimurFlush\Phalclate\Entity\Language::class, [
                    'getLanguage' => 'en'
                ])
            ]
        );
        $adapter->setCurrentLanguage('en');

        $this->assertEquals('en', $adapter->getCurrentLanguage());
    }

    /**
     * @depends testAccessorsForCurrentLanguageProperty
     */
    public function testThrowingExceptionInConstructWhenParameterCurrentLanguageIsNotPassed()
    {
        $this->tester->wantToTest('Throwing exception in construct when parameter \'currentLanguage\' is not passed.');

        $adapter = $this->makeEmpty(
            \TimurFlush\Phalclate\AdapterInterface::class,
            [
                'isReady' => true
            ]
        );

        $this->expectExceptionMessage('Parameter \'currentLanguage\' is not passed.');
        (new \TimurFlush\Phalclate\Manager(
            $adapter,
            [
                'baseLanguages' => [
                    new \TimurFlush\Phalclate\Entity\Language('ru')
                ]
            ]
        ));
    }

    public function testThrowingExceptionWhenPassedDialectIsNotValidInSetterForCurrentDialectProperty()
    {
        $this->tester->wantToTest(
            'Throwing exception when passed dialect is not valid in setter for current dialect property'
        );

        $reflector = new ReflectionClass(\TimurFlush\Phalclate\Manager::class);

        /**
         * @var $adapter \TimurFlush\Phalclate\Manager
         */
        $adapter = $reflector->newInstanceWithoutConstructor();

        $this->expectExceptionMessage('Passed dialect is not valid.');
        $adapter->setCurrentDialect('BRAKE_+\/');
    }

    public function testThrowingExceptionWhenCurrentLanguageIsNotSetInSetterForCurrentDialectProperty()
    {
        $this->tester->wantToTest(
            'Throwing exception when current language is not set in setter for current dialect property'
        );

        $reflector = new ReflectionClass(\TimurFlush\Phalclate\Manager::class);

        /**
         * @var $adapter \TimurFlush\Phalclate\Manager
         */
        $adapter = $reflector->newInstanceWithoutConstructor();

        $this->expectExceptionMessage('You cannot specify a dialect until the current language is not set.');
        $adapter->setCurrentDialect('ru');
    }

    /**
     * @depends testAccessorsForCurrentLanguageProperty
     * @depends testThrowingExceptionInConstructWhenParameterCurrentLanguageIsNotPassed
     * @depends testThrowingExceptionWhenPassedDialectIsNotValidInSetterForCurrentDialectProperty
     */
    public function testAccessorsForCurrentDialectProperty()
    {
        $this->tester->wantToTest(
            'Accessors for \'_currentDialect\' property.'
        );

        $reflector = new ReflectionClass(\TimurFlush\Phalclate\Manager::class);

        /**
         * @var $adapter \TimurFlush\Phalclate\Manager
         */
        $adapter = $reflector->newInstanceWithoutConstructor();

        $this->assertNull($adapter->getCurrentDialect());

        $adapter->setBaseLanguages(
            [
                $this->makeEmpty(\TimurFlush\Phalclate\Entity\Language::class, [
                    'getLanguage' => 'en',
                    'getDialects' => [
                        'US' => new \stdClass()
                    ]
                ])
            ]
        );
        $adapter->setCurrentLanguage('en');
        $adapter->setCurrentDialect('US');

        $this->assertEquals('US', $adapter->getCurrentDialect());
    }

    /**
     * @depends testAccessorsForCurrentLanguageProperty
     */
    public function testSettingCurrentLanguageInConstruct()
    {
        $this->tester->wantToTest('Setting current language in construct.');

        $adapter = $this->makeEmpty(
            \TimurFlush\Phalclate\AdapterInterface::class,
            [
                'isReady' => true
            ]
        );

        $manager = new \TimurFlush\Phalclate\Manager(
            $adapter,
            [
                'baseLanguages' => [
                    new \TimurFlush\Phalclate\Entity\Language('en')
                ],
                'currentLanguage' => 'en',
            ]
        );

        $this->assertEquals('en', $manager->getCurrentLanguage());
    }

    /**
     * @depends testAccessorsForCurrentDialectProperty
     */
    public function testSettingCurrentDialectInConstruct()
    {
        $this->tester->wantToTest('Setting current dialect in construct.');

        $adapter = $this->makeEmpty(
            \TimurFlush\Phalclate\AdapterInterface::class,
            [
                'isReady' => true
            ]
        );

        $manager = new \TimurFlush\Phalclate\Manager(
            $adapter,
            [
                'baseLanguages' => [
                    $this->make(\TimurFlush\Phalclate\Entity\Language::class, [
                        'getLanguage' => 'ru',
                        'getDialects' => [
                            'RU' => new \stdClass()
                        ]
                    ])
                ],
                'currentLanguage' => 'ru',
                'currentDialect' => 'RU'
            ]
        );

        $this->assertEquals('RU', $manager->getCurrentDialect());
    }

    public function testAccessorsForFailOverTranslationProperty()
    {
        $this->tester->wantToTest('Accessors for fail over translation property.');

        $reflector = new ReflectionClass(\TimurFlush\Phalclate\Manager::class);

        /**
         * @var $adapter \TimurFlush\Phalclate\Manager
         */
        $adapter = $reflector->newInstanceWithoutConstructor();

        $adapter->setFailOverTranslation('someFOT');
        $this->assertEquals('someFOT', $adapter->getFailOverTranslation());
    }

    /**
     * @depends testAccessorsForFailOverTranslationProperty
     */
    public function testSettingFailOverTranslationInConstruct()
    {
        $this->tester->wantToTest('Setting fail over translation in construct.');

        $adapter = $this->makeEmpty(
            \TimurFlush\Phalclate\AdapterInterface::class,
            [
                'isReady' => true
            ]
        );

        $manager = new \TimurFlush\Phalclate\Manager(
            $adapter,
            [
                'baseLanguages' => [
                    $this->make(\TimurFlush\Phalclate\Entity\Language::class, [
                        'getLanguage' => 'ru',
                        'getDialects' => [
                            'RU' => new \stdClass()
                        ]
                    ])
                ],
                'currentLanguage' => 'ru',
                'currentDialect' => 'RU',
                'failOverTranslation' => 'Kavoooooo?'
            ]
        );

        $this->assertEquals('Kavoooooo?', $manager->getFailOverTranslation());
    }

    public function testAccessorsForCacheProperty()
    {
        $this->tester->wantToTest('Accessors for cache property.');

        $reflector = new ReflectionClass(\TimurFlush\Phalclate\Manager::class);

        /**
         * @var $adapter \TimurFlush\Phalclate\Manager
         */
        $adapter = $reflector->newInstanceWithoutConstructor();

        $this->assertNull($adapter->getCache());

        $adapter->setCache(
            $this->makeEmpty(\Phalcon\Cache\BackendInterface::class)
        );

        $this->assertInstanceOf(\Phalcon\Cache\BackendInterface::class, $adapter->getCache());
    }

    /**
     * @depends testAccessorsForFailOverTranslationProperty
     */
    public function testSettingCacheInConstruct()
    {
        $this->tester->wantToTest('Setting cache in construct.');

        $adapter = $this->makeEmpty(
            \TimurFlush\Phalclate\AdapterInterface::class,
            [
                'isReady' => true
            ]
        );

        $manager = new \TimurFlush\Phalclate\Manager(
            $adapter,
            [
                'baseLanguages' => [
                    $this->make(\TimurFlush\Phalclate\Entity\Language::class, [
                        'getLanguage' => 'ru',
                        'getDialects' => [
                            'RU' => new \stdClass()
                        ]
                    ])
                ],
                'currentLanguage' => 'ru',
                'currentDialect' => 'RU',
                'cache' => $this->makeEmpty(\Phalcon\Cache\BackendInterface::class),
            ]
        );

        $this->assertInstanceOf(\Phalcon\Cache\BackendInterface::class, $manager->getCache());
    }

    public function testThrowingExceptionWhenPassedKeyIsNotValidInGettingTranslation()
    {
        $this->tester->wantToTest('Getting translation.');

        $adapter = $this->makeEmpty(
            \TimurFlush\Phalclate\AdapterInterface::class,
            [
                'isReady' => true,
            ]
        );

        $manager = new \TimurFlush\Phalclate\Manager(
            $adapter,
            [
                'baseLanguages' => [
                    $this->make(\TimurFlush\Phalclate\Entity\Language::class, [
                        'getLanguage' => 'ru',
                        'getDialects' => [
                            'RU' => new \stdClass()
                        ]
                    ])
                ],
                'currentLanguage' => 'ru'
            ]
        );

        $this->expectExceptionMessageRegExp('/Passed the invalid translation key/');
        $manager->getTranslation('+-.\';');
    }

    /**
     * @depends testAccessorsForFailOverTranslationProperty
     */
    public function testGettingTranslationTheCaseIsReturnFailOverTranslationTheFirstSubCase()
    {
        $this->tester->wantToTest('Getting translation the case is \'return fail over translation\' first sub case.');

        $adapter = $this->makeEmpty(
            \TimurFlush\Phalclate\AdapterInterface::class,
            [
                'isReady' => true,
                'getTranslation' => null
            ]
        );

        $manager = new \TimurFlush\Phalclate\Manager(
            $adapter,
            [
                'baseLanguages' => [
                    $this->make(\TimurFlush\Phalclate\Entity\Language::class, [
                        'getLanguage' => 'ru'
                    ])
                ],
                'currentLanguage' => 'ru',
                'failOverTranslation' => 'firstSubCase',
            ]
        );

        $this->assertEquals(
            'firstSubCase',
            $manager->getTranslation('someKey')
        );
    }

    public function testGettingTranslationTheCaseIsReturnFailOverTranslationTheSecondSubCase()
    {
        $this->tester->wantToTest('Getting translation the case is \'return fail over translation\' second sub case.');

        $adapter = $this->makeEmpty(
            \TimurFlush\Phalclate\AdapterInterface::class,
            [
                'isReady' => true,
                'getTranslation' => null
            ]
        );

        $manager = new \TimurFlush\Phalclate\Manager(
            $adapter,
            [
                'baseLanguages' => [
                    $this->make(\TimurFlush\Phalclate\Entity\Language::class, [
                        'getLanguage' => 'ru'
                    ])
                ],
                'currentLanguage' => 'ru',
            ]
        );

        $this->assertEquals(
            'secondSubCase',
            $manager->getTranslation('someKey', 'secondSubCase')
        );
    }

    public function testGettingTranslationTheCaseIsReplacePlaceholder()
    {
        $this->tester->wantToTest('Getting translation the case is \'replace placeholders\'.');

        $adapter = $this->makeEmpty(
            \TimurFlush\Phalclate\AdapterInterface::class,
            [
                'isReady' => true,
                'getTranslation' => 'Hello, %name%. I am %name2%.'
            ]
        );

        $manager = new \TimurFlush\Phalclate\Manager(
            $adapter,
            [
                'baseLanguages' => [
                    $this->make(\TimurFlush\Phalclate\Entity\Language::class, [
                        'getLanguage' => 'ru'
                    ])
                ],
                'currentLanguage' => 'ru',
            ]
        );

        $this->assertEquals(
            'Hello, Flush. I am CockSucker.',
            $manager->getTranslation(
                'someKey',
                [
                    'name' => 'Flush',
                    'name2' => 'CockSucker'
                ]
            )
        );
    }

    public function testGettingTranslationTheCaseIsEmptyTranslation()
    {
        $this->tester->wantToTest('Getting translation the case is \'empty translation\'.');

        $adapter = $this->makeEmpty(
            \TimurFlush\Phalclate\AdapterInterface::class,
            [
                'isReady' => true,
                'getTranslation' => ''
            ]
        );

        $manager = new \TimurFlush\Phalclate\Manager(
            $adapter,
            [
                'baseLanguages' => [
                    $this->make(\TimurFlush\Phalclate\Entity\Language::class, [
                        'getLanguage' => 'ru'
                    ])
                ],
                'currentLanguage' => 'ru',
            ]
        );

        $this->assertEquals('', $manager->getTranslation('someKey'));
    }

    public function testGettingTranslationTheCaseIsFirstFetchModeIsInvoked()
    {
        $this->tester->wantToTest('Getting translation the case is \'first fetch mode is invoked\'.');

        $invoked = false;

        $adapter = $this->makeEmpty(
            \TimurFlush\Phalclate\AdapterInterface::class,
            [
                'isReady' => true,
                'getTranslation' => function (
                    string $key,
                    string $language,
                    ?string $dialect = null,
                    bool $firstFetch = false
                ) use (&$invoked) {
                    if ($firstFetch) {
                        $invoked = true;
                    }
                    return 'someTranslation';
                }
            ]
        );

        $manager = new \TimurFlush\Phalclate\Manager(
            $adapter,
            [
                'baseLanguages' => [
                    $this->make(\TimurFlush\Phalclate\Entity\Language::class, [
                        'getLanguage' => 'ru'
                    ])
                ],
                'currentLanguage' => 'ru',
            ]
        );

        ($manager->getTranslation('someKey', true));
        $this->assertTrue($invoked);
    }

    /**
     * @depends testGettingTranslationTheCaseIsReturnFailOverTranslationTheFirstSubCase
     * @depends testGettingTranslationTheCaseIsReturnFailOverTranslationTheSecondSubCase
     */
    public function testGettingTranslationTheCaseIsUseCacheAndReturnNull()
    {
        $this->tester->wantToTest('Getting translation the case is \'use cache and return null\'.');

        $adapter = $this->makeEmpty(
            \TimurFlush\Phalclate\AdapterInterface::class,
            [
                'isReady' => true,
                'getTranslation' => null
            ]
        );

        $manager = new \TimurFlush\Phalclate\Manager(
            $adapter,
            [
                'baseLanguages' => [
                    $this->make(\TimurFlush\Phalclate\Entity\Language::class, [
                        'getLanguage' => 'ru'
                    ])
                ],
                'currentLanguage' => 'ru',
                'failOverTranslation' => 'Shit',
                'cache' => $this->makeEmpty(\Phalcon\Cache\BackendInterface::class, [
                    'get' => null
                ])
            ]
        );

        $this->assertEquals(
            'Shit',
            $manager->getTranslation('someKey')
        );
    }

    /**
     * @depends testGettingTranslationTheCaseIsReturnFailOverTranslationTheFirstSubCase
     * @depends testGettingTranslationTheCaseIsReturnFailOverTranslationTheSecondSubCase
     */
    public function testGettingTranslationTheCaseIsUseCacheAndReturnNotNullEitherSaving()
    {
        $this->tester->wantToTest('Getting translation the case is \'use cache and return not null either saving\'.');

        $adapter = $this->makeEmpty(
            \TimurFlush\Phalclate\AdapterInterface::class,
            [
                'isReady' => true,
                'getTranslation' => 'notNull'
            ]
        );

        $manager = new \TimurFlush\Phalclate\Manager(
            $adapter,
            [
                'baseLanguages' => [
                    $this->make(\TimurFlush\Phalclate\Entity\Language::class, [
                        'getLanguage' => 'ru'
                    ])
                ],
                'currentLanguage' => 'ru',
                'failOverTranslation' => 'Shit',
                'cache' => $this->makeEmpty(\Phalcon\Cache\BackendInterface::class, [
                    'get' => null,
                    'save' => \Codeception\Stub\Expected::once(true)
                ])
            ]
        );

        $this->assertEquals(
            'notNull',
            $manager->getTranslation('someKey')
        );
    }

    public function testGettingTranslationTheCaseIsPassingCorrectKeyWithoutCache()
    {
        $this->tester->wantToTest('Getting translation the case is \'passing correct key without cache\'.');

        $passedCorrectKey = false;

        $adapter = $this->makeEmpty(
            \TimurFlush\Phalclate\AdapterInterface::class,
            [
                'isReady' => true,
                'getTranslation' => function (
                    string $key,
                    string $language,
                    ?string $dialect = null,
                    bool $firstFetch = false
                ) use (&$passedCorrectKey) {
                    if ($key === 'someKey') {
                        $passedCorrectKey = true;
                    }
                    return 'someTranslation';
                }
            ]
        );

        $manager = new \TimurFlush\Phalclate\Manager(
            $adapter,
            [
                'baseLanguages' => [
                    $this->make(\TimurFlush\Phalclate\Entity\Language::class, [
                        'getLanguage' => 'ru'
                    ])
                ],
                'currentLanguage' => 'ru',
            ]
        );

        ($manager->getTranslation('someKey', true));
        $this->assertTrue($passedCorrectKey);
    }

    public function testGettingTranslationTheCaseIsPassingCorrectKeyWithCache()
    {
        $this->tester->wantToTest('Getting translation the case is \'passing correct key with cache\'.');

        $passedCorrectKeyToAdapter = false;
        $passedCorrectKeyToCache = false;

        $adapter = $this->makeEmpty(
            \TimurFlush\Phalclate\AdapterInterface::class,
            [
                'isReady' => true,
                'getTranslation' => function (
                    string $key,
                    string $language,
                    ?string $dialect = null,
                    bool $firstFetch = false
                ) use (&$passedCorrectKeyToAdapter) {
                    if ($key === 'AnalToy') {
                        $passedCorrectKeyToAdapter = true;
                    }
                    return 'someInfo';
                }
            ]
        );

        $manager = new \TimurFlush\Phalclate\Manager(
            $adapter,
            [
                'baseLanguages' => [
                    $this->make(\TimurFlush\Phalclate\Entity\Language::class, [
                        'getLanguage' => 'ru'
                    ])
                ],
                'currentLanguage' => 'ru',
                'cache' => $this->makeEmpty(\Phalcon\Cache\BackendInterface::class, [
                    'get' => \Codeception\Stub\Expected::atLeastOnce(function ($key) use (&$passedCorrectKeyToCache) {
                        if (mb_strpos($key, 'AnalToy') !== false) {
                            $passedCorrectKeyToCache = true;
                        }
                        return null;
                    }),

                    'save' => \Codeception\Stub\Expected::atLeastOnce(function ($key) use (&$passedCorrectKeyToCache) {
                        if (mb_strpos($key, 'AnalToy') !== false) {
                            $passedCorrectKeyToCache = true;
                        }
                        return true;
                    })
                ])
            ]
        );

        ($manager->getTranslation('AnalToy', true));
        $this->assertTrue($passedCorrectKeyToAdapter && $passedCorrectKeyToCache);
    }
}