<?php
declare(strict_types=1);

namespace EonX\EasyCore\Lock;

use Closure;

trait ProcessWithLockTrait
{
    /**
     * @var \EonX\EasyCore\Lock\LockServiceInterface
     */
    private $lockService;

    /**
     * @required
     */
    public function setLockService(LockServiceInterface $lockService): void
    {
        $this->lockService = $lockService;
    }

    /**
     * @return null|mixed
     */
    protected function processWithLock(WithLockDataInterface $withLockData, Closure $func)
    {
        $data = $withLockData->getLockData();
        $lock = $this->lockService->createLock($data->getResource(), $data->getTtl());

        if ($lock->acquire() === false) {
            return null;
        }

        try {
            return $func();
        } finally {
            $lock->release();
        }
    }
}
