<?php
namespace RazonYang\Yii2\RateLimiter\Tests\Unit;

use Codeception\Test\Unit;
use RazonYang\Yii2\RateLimiter\MemcachedRateLimiter;

class MemcachedRateLimiterTest extends Unit
{
    /**
     * @dataProvider dataSetUp
     */
    public function testSetUp(array $config): void
    {
        $limiter = new MemcachedRateLimiter($config);
        foreach ($config as $name => $val) {
            $this->assertSame($val, $limiter->$name);
        }
    }

    public function dataSetUp(): array
    {
        return [
            [['ttl' => 0, 'prefix' => 'foo']],
            [['ttl' => 60, 'prefix' => 'bar']],
        ];
    }
}
