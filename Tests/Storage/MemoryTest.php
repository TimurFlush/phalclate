<?php

namespace TimurFlush\Phalclate\Tests;

use PHPUnit\Framework\TestCase;
use TimurFlush\Phalclate\Storage\Memory;
use TimurFlush\Phalclate\StorageInterface;

class MemoryTest extends TestCase
{
    /**
     * @var StorageInterface
     */
    private $storage;

    public function setUp()
    {
        $this->storage = new Memory();
    }

    public function testSaveAndGet()
    {
        $expectedKey = 'key';
        $expectedValue = 'value';

        $this->storage->save($expectedKey, $expectedValue);
        $chunkStorage = $this->storage->get($expectedKey);
        $fullStorage = $this->storage->get();

        $this->assertTrue($chunkStorage === $expectedValue, 'Memory was not saved.');
        $this->assertTrue(isset($fullStorage[$expectedKey]) && $fullStorage[$expectedKey] === $expectedValue,
            'Memory was not saved.'
        );
    }

    public function testExists()
    {
        $expectedKey = 'key';
        $expectedValue = 'value';

        $this->storage->save($expectedKey, $expectedValue);
        $exists = $this->storage->exists($expectedKey);

        $this->assertTrue($exists === true, 'Saved information was not found.');
    }

    public function testRemove()
    {
        $expectedKey = 'key';
        $expectedValue = 'value';

        $this->storage->save($expectedKey, $expectedValue);
        $this->storage->remove($expectedKey);

        $this->assertTrue($this->storage->get($expectedKey) === null, 'Can not remove item.');
    }

    public function testFlush()
    {
        $expectedKey = 'key';
        $expectedValue = 'value';

        $this->storage->save($expectedKey, $expectedValue);
        $this->storage->flush();

        $this->assertTrue($this->storage->get() === [], 'Can not flush memory.');
    }

    public function tearDown()
    {
        unset($this->storage);
    }
}