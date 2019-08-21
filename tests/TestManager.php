<?php
namespace RazonYang\Yii2\RateLimiter\Tests;

use RazonYang\TokenBucket\Manager;

class TestManager extends Manager
{
    public $data;

    protected function load(string $name)
    {
        return $this->data[$name] ?? false;
    }

    protected function save(string $name, $value)
    {
        $this->data[$name] = $value;
    }
}
