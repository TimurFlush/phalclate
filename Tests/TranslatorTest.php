<?php

namespace TimurFlush\Phalclate\Tests;

use PHPUnit\Framework\TestCase;

class TranslatorTest extends TestCase
{
    private $translatorMock;

    public function setUp()
    {
        $this->translatorMock = $this->getMockForAbstractClass(\TimurFlush\Phalclate\Translator::class);
    }

    public function testSetOptionsAndGetOptions()
    {
        $options = ['test' => 'test'];

        $this->translatorMock->setOptions($options);
        $this->assertTrue(
            md5(json_encode($this->translatorMock->getOptions())) === md5(json_encode($options))
        , 'Hashes the passed options and expected do not match.');
    }

    public function tearDown()
    {
        unset($this->translatorMock);
    }
}