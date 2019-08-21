<?php
namespace RazonYang\Yii2\RateLimiter\Tests\Unit;

use Codeception\Test\Unit;
use RazonYang\Yii2\RateLimiter\BaseRateLimiter;
use RazonYang\Yii2\RateLimiter\Tests\TestRateLimiter;
use RazonYang\Yii2\RateLimiter\Tests\TestUser;
use yii\base\Action;
use yii\web\Controller;
use Yii;
use yii\web\TooManyRequestsHttpException;

class RateLimiterTest extends Unit
{
    private function createRateLimiter(int $capacity = 10, $rate = 1): TestRateLimiter
    {
        return new TestRateLimiter(['capacity' => $capacity, 'rate' => $rate]);
    }

    public function testGetLogger(): void
    {
        $limiter = $this->createRateLimiter();
        $property = new \ReflectionProperty(BaseRateLimiter::class, 'logger');
        $property->setAccessible(true);

        $method = new \ReflectionMethod(TestRateLimiter::class, 'getLogger');
        $method->setAccessible(true);
        $this->assertSame($property->getValue($limiter), $method->invoke($limiter));
    }

    public function testGetName(): void
    {
        $limiter = $this->createRateLimiter();
        $action = $this->createAction('test');
        $request = Yii::$app->getRequest();
        $user = Yii::$app->getUser();

        $this->assertSame($request->getUserIP() . ':' . $action->getUniqueId(), $limiter->getName($user, $request, $action));

        $identity = new TestUser();
        $identity->id = 'foo';
        $user->login($identity);
        $this->assertSame('foo:' . $action->getUniqueId(), $limiter->getName($user, $request, $action));
    }

    public function testNameCallback(): void
    {
        $name = uniqid();
        $callback = function () use ($name) {
            return $name;
        };
        $limiter = new TestRateLimiter([
            'nameCallback' => $callback,
        ]);
        $action = $this->createAction('test');
        $this->assertSame($name, $limiter->getName(Yii::$app->getUser(), Yii::$app->getRequest(), $action));
    }

    public function testBeforeAction(): void
    {
        $limiter = $this->createRateLimiter(1, 10);
        $action = $this->createAction('test');
        $this->assertTrue($limiter->beforeAction($action));
        $headers = $limiter->response->getHeaders();
        $this->assertTrue($headers->has('X-Rate-Limit-Limit'));
        $this->assertTrue($headers->has('X-Rate-Limit-Remaining'));
        $this->assertTrue($headers->has('X-Rate-Limit-Reset'));

        $this->expectException(TooManyRequestsHttpException::class);
        $limiter->beforeAction($action);
    }

    public function createAction(string $id): Action
    {
        return new Action($id, new class('test', Yii::$app) extends Controller {
        });
    }
}
