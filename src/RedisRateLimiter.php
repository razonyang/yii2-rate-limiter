<?php
namespace RazonYang\Yii2\RateLimiter;

use RazonYang\TokenBucket\ManagerInterface;
use RazonYang\TokenBucket\Manager\RedisManager;
use RazonYang\Yii2\RateLimiter\BaseRateLimiter;

class RedisRateLimiter extends BaseRateLimiter
{
    /**
     * @var string $hostname
     */
    public $hostname = 'localhost';

    /**
     * @var int $port
     */
    public $port = 6379;

    /**
     * @var string $password
     */
    public $password = '';

    public $ttl = 3600;

    public $prefix = 'rate_limiter:';

    protected function initManager(): ManagerInterface
    {
        return new RedisManager($this->capacity, $this->rate, $this->getLogger(), $this->getRedis(), $this->ttl, $this->prefix);
    }

    private $redis;

    protected function getRedis(): \Redis
    {
        if ($this->redis === null) {
            $this->redis = new \Redis();
            $this->redis->connect($this->hostname, $this->port);
            if ($this->password !== '') {
                $this->redis->auth($this->password);
            }
        }

        return $this->redis;
    }
}
