<?php
namespace RazonYang\Yii2\RateLimiter;

use yii\base\Action;
use yii\filters\RateLimiter;
use yii\web\IdentityInterface;
use yii\web\Request;
use yii\web\TooManyRequestsHttpException;
use yii\web\User;
use Psr\Log\LoggerInterface;
use RazonYang\TokenBucket\ManagerInterface;
use RazonYang\Yii2\Psr\Log\Logger;
use Yii;

abstract class BaseRateLimiter extends RateLimiter
{
    /**
     * @var User $user
     */
    public $user;

    /**
     * @var int $capacity token bucket capacity.
     */
    public $capacity = 5000;

    /**
     * @var float $rate token rate.
     */
    public $rate = 0.72;

    /**
     * @var \Closure $nameCallback bucket name callback.
     */
    public $nameCallback;

    /**
     * @var int $limitPeriod calculates the maximum count of requests during the period.
     */
    public $limitPeriod = 3600;

    /**
     * @var ManagerInterface $manager
     */
    private $manager;

    public function init()
    {
        parent::init();

        if ($this->user === null) {
            $this->user = Yii::$app->getUser();
        }

        $this->manager = $this->initManager();
    }

    abstract protected function initManager(): ManagerInterface;

    public function beforeAction($action)
    {
        $limit = $this->manager->getLimit($this->limitPeriod);

        $name = $this->getName($this->user, $this->request, $action);
        $comsumed = $this->manager->consume($name, $remaining, $reset);
        $this->addRateLimitHeaders($this->response, $limit, $remaining, $reset);
        if (!$comsumed) {
            throw new TooManyRequestsHttpException($this->errorMessage);
        }

        return true;
    }

    /**
     * Returns the bucket name.
     *
     * @param User $user
     * @param Request $request
     * @param Action $action
     */
    public function getName(User $user, Request $request, Action $action): string
    {
        if ($this->nameCallback !== null) {
            return call_user_func($this->nameCallback, $user, $request, $action);
        }

        $route = $action->getUniqueId();
        if (!$user->getIsGuest()) {
            return $user->getId() . ':' . $route;
        }

        return $request->getUserIP() . ':' . $route;
    }

    /**
     * @var LoggerInterface
     */
    private $logger;

    protected function getLogger(): LoggerInterface
    {
        if ($this->logger === null) {
            $this->logger = new Logger();
        }
        
        return $this->logger;
    }
}
