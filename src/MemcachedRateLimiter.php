<?php
namespace RazonYang\Yii2\RateLimiter;

use RazonYang\TokenBucket\Manager\MemcachedManager;
use RazonYang\TokenBucket\ManagerInterface;
use RazonYang\Yii2\RateLimiter\BaseRateLimiter;

class MemcachedRateLimiter extends BaseRateLimiter
{
    /**
     * @var string $hostname
     */
    public $hostname = 'localhost';

    /**
     * @var int $port
     */
    public $port = 11211;

    public $ttl = 3600;

    public $prefix = 'rate_limiter:';

    protected function initManager(): ManagerInterface
    {
        return new MemcachedManager($this->capacity, $this->rate, $this->getLogger(), $this->getMemcached(), $this->ttl, $this->prefix);
    }

    private $memcached;

    protected function getMemcached(): \Memcached
    {
        if ($this->memcached === null) {
            $this->memcached = new \Memcached();
            $this->memcached->addServer($this->hostname, $this->port);
        }

        return $this->memcached;
    }
}
