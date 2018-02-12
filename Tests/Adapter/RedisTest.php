<?php

namespace TimurFlush\Phalclate\Tests\Adapter;

use Faker\Factory;
use TimurFlush\Phalclate\Adapter\Redis;
use PHPUnit\Framework\TestCase;

class RedisTest extends TestCase
{
    /**
     * @var \TimurFlush\Phalclate\Adapter\Redis
     */
    private $adapter;

    /**
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * @var string Source language.
     */
    private $from = 'ru';

    /**
     * @var string Translate language.
     */
    private $to = 'en';

    /**
     * @var int
     */
    private $redisPort = 6379;

    public function setUp()
    {
        $this->faker = Factory::create();
        $this->adapter = new Redis([
            'current_language' => $this->to,
            'default_language' => $this->from,

            'cache_directory' => dirname(__DIR__) . '/Cache/',

            'host' => 'localhost',
            'port' => $this->redisPort,
        ]);
    }

    private function getSocketResource()
    {
        return @stream_socket_client('tcp://localhost:' . $this->redisPort);
    }

    private function closeSocketResource($res) : void
    {
        @fclose($res);
    }

    public function testSave()
    {
        $resource = $this->getSocketResource();
        if (!$resource)
            $this->markTestSkipped("Redis on port {$this->redisPort} not available.");
        $this->closeSocketResource($resource);

        $fake = [
            'boys' => [
                $this->faker->firstNameMale => $this->faker->phoneNumber,
                $this->faker->firstNameMale => $this->faker->phoneNumber
            ],
            'girls' => [
                $this->faker->firstNameFemale => $this->faker->phoneNumber,
                $this->faker->firstNameFemale => $this->faker->phoneNumber
            ]
        ];

        $save = $this->adapter->saveCache($this->from, $this->to, $fake);
        $cache = $this->adapter->getCache($this->from, $this->to);

        $this->assertTrue($save && md5(json_encode($fake)) === md5(json_encode($cache)), "Cache was not saved.");
    }

    public function testFlush()
    {
        $resource = $this->getSocketResource();
        if (!$resource)
            $this->markTestSkipped("Redis on port {$this->redisPort} not available.");
        $this->closeSocketResource($resource);

        $flush = $this->adapter->flushCache($this->from, $this->to);
        $cache = $this->adapter->getCache($this->from, $this->to);

        $this->assertTrue($flush && !sizeof(get_object_vars($cache)), 'Cache was not flushed.');
    }

    public function testSaveGroup()
    {
        $resource = $this->getSocketResource();
        if (!$resource)
            $this->markTestSkipped("Redis on port {$this->redisPort} not available.");
        $this->closeSocketResource($resource);

        $fakeBoys = [
            $this->faker->firstNameMale => $this->faker->phoneNumber,
            $this->faker->firstNameMale => $this->faker->phoneNumber,
        ];
        $fakeGirls = [
            $this->faker->firstNameFemale => $this->faker->phoneNumber,
            $this->faker->firstNameMale => $this->faker->phoneNumber,
        ];

        $saveBoys = $this->adapter->saveCache($this->from, $this->to, $fakeBoys, false, 'boys');
        $saveGirls = $this->adapter->saveCache($this->from, $this->to, $fakeGirls, false, 'girls');

        $cacheAfterSaving = $this->adapter->getCache($this->from, $this->to);

        $this->assertTrue($saveBoys && $saveGirls, 'Cache was not saved.');
        $this->assertTrue(md5(json_encode($fakeBoys)) == md5(json_encode((array)$cacheAfterSaving->boys))
            && md5(json_encode($fakeGirls)) == md5(json_encode((array)$cacheAfterSaving->girls)), 'Groups was not found after saving.');

        $this->adapter->flushCache($this->from, $this->to);
    }

    public function testSaveOnlyReplace()
    {
        $resource = $this->getSocketResource();
        if (!$resource)
            $this->markTestSkipped("Redis on port {$this->redisPort} not available.");
        $this->closeSocketResource($resource);

        $firstFake = [
            'boys' => [
                $this->faker->firstNameMale => $this->faker->phoneNumber,
                $this->faker->firstNameMale => $this->faker->phoneNumber
            ],
            'girls' => [
                $this->faker->firstNameFemale => $this->faker->phoneNumber,
                $this->faker->firstNameFemale => $this->faker->phoneNumber
            ]
        ];

        $this->adapter->saveCache($this->from, $this->to, $firstFake);

        $secondFake = [];
        for($i=0;$i<6;$i++){
            $secondFake['boys'][$this->faker->firstNameMale] = $this->faker->ipv4;
            $secondFake['girls'][$this->faker->firstNameFemale] = $this->faker->ipv4;
        }

        $save = $this->adapter->saveCache($this->from, $this->to, $secondFake, true);
        $cache = $this->adapter->getCache($this->from, $this->to);

        $this->assertTrue($save, "Cache was not saved.");
        $this->assertTrue(md5(json_encode(array_keys($firstFake['boys'])))
            == md5(json_encode(array_keys(get_object_vars($cache->boys)))), "A hash sum of the first and the second state are not the same.");
        $this->assertTrue(md5(json_encode(array_keys($firstFake['girls'])))
            == md5(json_encode(array_keys(get_object_vars($cache->girls)))), "A hash sum of the first and the second state are not the same.");
        $this->adapter->flushCache($this->from, $this->to);
    }

    public function testGetGroupsAndGetGroup()
    {
        $resource = $this->getSocketResource();
        if (!$resource)
            $this->markTestSkipped("Redis on port {$this->redisPort} not available.");
        $this->closeSocketResource($resource);

        $fake = [
            'boys' => [
                $this->faker->firstNameMale => $this->faker->phoneNumber,
                $this->faker->firstNameMale => $this->faker->phoneNumber
            ],
            'girls' => [
                $this->faker->firstNameFemale => $this->faker->phoneNumber,
                $this->faker->firstNameFemale => $this->faker->phoneNumber
            ]
        ];

        $save = $this->adapter->saveCache($this->from, $this->to, $fake);
        $groupBoys = $this->adapter->getGroup($this->from, $this->to, 'boys');
        $groupGirls = $this->adapter->getGroup($this->from, $this->to, 'girls');

        $this->assertTrue($save, 'Cache was not saved.');
        $this->assertTrue(md5(json_encode($groupBoys)) === md5(json_encode($fake['boys'])), 'A Hashes of groups is not equally.');
        $this->assertTrue(md5(json_encode($groupGirls)) === md5(json_encode($fake['girls'])), 'A Hashes of groups is not equally.');
        $this->adapter->flushCache($this->from, $this->to);
    }

    public function test_()
    {
        $resource = $this->getSocketResource();
        if (!$resource)
            $this->markTestSkipped("Redis on port {$this->redisPort} not available.");
        $this->closeSocketResource($resource);

        $_ = $this->adapter->_('Мальчик', $this->from);
        if ($_ === '') $this->markTestSkipped('Translators are unavailable, try again.');
        $this->assertTrue(strtolower($_) === 'boy');
        $this->adapter->flushCache($this->from, $this->to);
    }

    public function testRemoveGroup()
    {
        $resource = $this->getSocketResource();
        if (!$resource)
            $this->markTestSkipped("Redis on port {$this->redisPort} not available.");
        $this->closeSocketResource($resource);

        $supposeArray = [
            'group1' => [
                'alpha' => 'a'
            ]
        ];

        $save = $this->adapter->saveCache($this->from, $this->to, [
            'group1' => $supposeArray['group1'],
            'group2' => [
                'alpha' => 'a',
                'gamma' => 'g'
            ]
        ]);

        $this->adapter->removeGroup($this->from, $this->to, 'group2');

        $cache = $this->adapter->getCache($this->from, $this->to);

        $this->assertTrue($save, 'Cache was not saved.');
        $this->assertTrue(md5(json_encode($supposeArray)) == md5(json_encode($cache)),
            'A hash sum of the first and the second state are not the same.'
        );
        $this->adapter->flushCache($this->from, $this->to);
    }

    public function testRemoveTranslate()
    {
        $resource = $this->getSocketResource();
        if (!$resource)
            $this->markTestSkipped("Redis on port {$this->redisPort} not available.");
        $this->closeSocketResource($resource);

        $supposeArray = [
            'group1' => [
                'alpha' => 'a'
            ],
            'group2' => [
                'beta' => 'b',
            ]
        ];

        $save = $this->adapter->saveCache($this->from, $this->to, [
            'group1' => $supposeArray['group1'],
            'group2' => array_merge($supposeArray['group2'], [
                'alpha' => 'a',
                'gamma' => 'g'
            ])
        ]);

        $this->adapter->removeTranslate($this->from, $this->to, 'group2', ['alpha', 'gamma']);

        $cache = $this->adapter->getCache($this->from, $this->to);

        $this->assertTrue($save, 'Cache was not saved.');
        $this->assertTrue(md5(json_encode($supposeArray)) == md5(json_encode($cache)),
            'A hash sum of the first and the second state are not the same.'
        );
        $this->adapter->flushCache($this->from, $this->to);
    }

    public function tearDown()
    {
        unset($this->adapter, $this->faker);
    }
}