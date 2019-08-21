<?php
namespace RazonYang\Yii2\RateLimiter\Redis;

use yii\di\Instance;
use yii\redis\Connection;
use RazonYang\TokenBucket\ManagerInterface;
use RazonYang\Yii2\RateLimiter\BaseRateLimiter;

class RateLimiter extends BaseRateLimiter
{
    /**
     * @var Connectioin
     */
    public $redis = 'redis';

    public $ttl = 3600;

    public $prefix = 'rate_limiter:';

    public function init()
    {
        $this->redis = Instance::ensure($this->redis, Connection::class);

        parent::init();
    }

    protected function initManager(): ManagerInterface
    {
        return new Manager($this->capacity, $this->rate, $this->getLogger(), $this->redis, $this->ttl, $this->prefix);
    }
}
