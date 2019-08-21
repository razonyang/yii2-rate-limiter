<?php
namespace RazonYang\Yii2\RateLimiter\Tests;

use RazonYang\TokenBucket\ManagerInterface;
use RazonYang\Yii2\RateLimiter\BaseRateLimiter;

class TestRateLimiter extends BaseRateLimiter
{
    protected function initManager(): ManagerInterface
    {
        return new TestManager($this->capacity, $this->rate, $this->getLogger());
    }
}
