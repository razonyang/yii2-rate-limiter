<?php
namespace RazonYang\Yii2\RateLimiter\Tests\Unit\Redis;

use yii\redis\Connection;
use Codeception\Test\Unit;
use Psr\Log\NullLogger;
use RazonYang\Yii2\RateLimiter\Redis\Manager;
use Yii;

class ManagerTest extends Unit
{
    private $capacity = 10;

    private $rate = 1;

    private $ttl = 60;

    private $prefix = 'test:';
    /**
     * @var Connection
     */
    private $redis;

    public function setUp(): void
    {
        parent::setUp();

        $this->redis = Yii::$app->get('redis');
    }

    public function tearDown(): void
    {
        $this->redis = null;

        parent::tearDown();
    }

    public function createManager(): Manager
    {
        return new Manager($this->capacity, $this->rate, new NullLogger(), $this->redis, $this->ttl, $this->prefix);
    }

    public function testSetUp(): void
    {
        $manager = $this->createManager();

        $ttl = new \ReflectionProperty(Manager::class, 'ttl');
        $ttl->setAccessible(true);
        $this->assertSame($this->ttl, $ttl->getValue($manager));

        $prefix = new \ReflectionProperty(Manager::class, 'prefix');
        $prefix->setAccessible(true);
        $this->assertSame($this->prefix, $prefix->getValue($manager));

        $conn = new \ReflectionProperty(Manager::class, 'conn');
        $conn->setAccessible(true);
        $this->assertSame($this->redis, $conn->getValue($manager));
    }

    public function testSaveAndLoad(): void
    {
        $manager = $this->createManager();
        $save = new \ReflectionMethod(Manager::class, 'save');
        $save->setAccessible(true);

        $load = new \ReflectionMethod(Manager::class, 'load');
        $load->setAccessible(true);

        $name = 'test';
        $data = uniqid();
        $save->invoke($manager, $name, $data);

        $this->assertSame($data, $load->invoke($manager, $name));

        $this->assertSame($data, $this->redis->get($this->prefix . $name));
    }

    public function testSaveWithoutTtl(): void
    {
        $manager = new Manager($this->capacity, $this->rate, new NullLogger(), $this->redis, 0, $this->prefix);
        $save = new \ReflectionMethod(Manager::class, 'save');
        $save->setAccessible(true);
        $name = 'test';
        $data = uniqid();
        $save->invoke($manager, $name, $data);
        $this->assertEquals(-1, $this->redis->ttl($this->prefix . $name));
    }
}
