<?php
namespace RazonYang\Yii2\RateLimiter\Redis;

use yii\redis\Connection;
use Psr\Log\LoggerInterface;
use RazonYang\TokenBucket\Manager as BaseManager;
use RazonYang\TokenBucket\SerializerInterface;

class Manager extends BaseManager
{
    /**
     * @var Connection $conn
     */
    private $conn;

    private $ttl = 0;

    private $prefix = '0';

    public function __construct(
        int $capacity,
        float $rate,
        LoggerInterface $logger,
        Connection $conn,
        int $ttl = 0,
        string $prefix = '',
        ?SerializerInterface $serializer = null
    ) {
        parent::__construct($capacity, $rate, $logger, $serializer);
        $this->conn = $conn;
        $this->ttl = $ttl;
        $this->prefix = $prefix;
    }

    protected function load(string $name)
    {
        return $this->conn->get($this->getKey($name));
    }

    protected function save(string $name, $value)
    {
        if ($this->ttl > 0) {
            $this->conn->setex($this->getKey($name), $this->ttl, $value);
        } else {
            $this->conn->set($this->getKey($name), $value);
        }
    }

    protected function getKey(string $name): string
    {
        return $this->prefix . $name;
    }
}
