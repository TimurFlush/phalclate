<?php

namespace TimurFlush\Phalclate\Tests\Translator;

use PHPUnit\Framework\TestCase;
use TimurFlush\Phalclate\Translator\Yandex;

class YandexTest extends TestCase
{
    /**
     * @var \TimurFlush\Phalclate\Translator\Google
     */
    private $translator;

    /**
     * @var string
     */
    private $from = 'ru';

    /**
     * @var string
     */
    private $to = 'en';

    public function setUp()
    {
        $this->translator = new Yandex([
            'cache_directory' => dirname(__DIR__) . '/Cache/'
        ]);
    }

    public function testTranslate()
    {
        $translate = $this->translator->translate($this->from, $this->to, 'Мальчик');

        if ($translate === null) $this->markTestSkipped('Yandex translator is not available. Try later.');

        $this->assertTrue($translate == 'Boy', "The translated expression is not equivalent to the original: " . (string)$translate);
    }
}