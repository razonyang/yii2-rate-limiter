<?php
namespace RazonYang\Yii2\RateLimiter\Tests\Unit\Redis;

use Codeception\Test\Unit;
use RazonYang\Yii2\RateLimiter\Redis\RateLimiter;
use Yii;

class RateLimiterTest extends Unit
{
    /**
     * @dataProvider dataSetUp
     */
    public function testSetUp(array $config): void
    {
        $limiter = new RateLimiter($config);
        $redis = $limiter->redis;
        $this->assertSame(Yii::$app->get('redis'), $redis);
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
