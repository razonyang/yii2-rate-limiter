Yii2 Rate Limiter
=================

[![Build Status](https://travis-ci.org/razonyang/yii2-rate-limiter.svg?branch=master)](https://travis-ci.org/razonyang/yii2-rate-limiter)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/razonyang/yii2-rate-limiter/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/razonyang/yii2-rate-limiter/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/razonyang/yii2-rate-limiter/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/razonyang/yii2-rate-limiter/?branch=master)
[![Latest Stable Version](https://img.shields.io/packagist/v/razonyang/yii2-rate-limiter.svg)](https://packagist.org/packages/razonyang/yii2-rate-limiter)
[![Total Downloads](https://img.shields.io/packagist/dt/razonyang/yii2-rate-limiter.svg)](https://packagist.org/packages/razonyang/yii2-rate-limiter)
[![LICENSE](https://img.shields.io/github/license/razonyang/yii2-rate-limiter)](LICENSE)


Backends
--------

- `Memcached` requires [memcached](https://www.php.net/manual/en/book.memcached.php) extension.
- `Redis` requires [redis](https://github.com/phpredis/phpredis) extension or [yiisoft/yii2-redis](https://github.com/yiisoft/yii2-redis) package.


Installation
------------

```
composer require razonyang/yii2-rate-limiter
```

Usage
-----

Let's take 5000 requests every hours as example:

```php
return [
    public function behaviors()
    {
        return [
            // redis via redis extension
            'rateLimiter' => [
                'class' => \RazonYang\Yii2\RateLimiter\RedisRateLimiter::class,
                'password' => '',
                'hostname' => 'localhost',
                'port' => 6379,
                'capacity' => 5000,
                'rate' => 0.72,
                'limitPeriod' => 3600,
                'prefix' => 'rate_limiter:',
                'ttl' => 3600,
                // 'nameCallback' => $callback,
            ],
            // redis via yii2-redis
            'rateLimiter' => [
                'class' => \RazonYang\Yii2\RateLimiter\Redis\RateLimiter::class,
                'redis' => 'redis', // redis component name or definition
                'capacity' => 5000,
                'rate' => 0.72,
                'limitPeriod' => 3600,
                'prefix' => 'rate_limiter:',
                'ttl' => 3600,
                // 'nameCallback' => $callback,
            ],

            // memcached
            'rateLimiter' => [
                'class' => \RazonYang\Yii2\RateLimiter\MemcachedRateLimiter::class,
                'hostname' => 'localhost',
                'port' => 11211,
                'capacity' => 5000,
                'rate' => 0.72,
                'limitPeriod' => 3600,
                'prefix' => 'rate_limiter:',
                'ttl' => 3600,
                // 'nameCallback' => $callback,
            ],
        ];
    }
];
```

`RateLimiter` takes `uid:route`(authorized) or `ip:route`(guest) as bucket name, you can also change this behavior via `nameCallback`:

```php
$nameCallback = function (
    \yii\web\User $user,
    \yii\web\Request $request,
    \yii\base\Action $action
): string {
    return 'bucket name';
}
```
