<?php

declare(strict_types=1);

namespace EonX\EasyCore\Bridge\Laravel\Listeners;

use Illuminate\Queue\Events\WorkerStopping;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * @deprecated since 3.3.12, will be removed in 4.0.
 * Use EonX\EasyAsync\Bridge\Laravel\Queue\QueueWorkerStoppingListener instead.
 */
final class QueueWorkerStoppingListener
{
    /**
     * @var mixed[]
     */
    private static $reasons = [
        12 => 'Memory exceeded',
    ];

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    public function __construct(?LoggerInterface $logger = null)
    {
        $this->logger = $logger ?? new NullLogger();
    }

    public function handle(WorkerStopping $event): void
    {
        $reason = static::$reasons[$event->status] ?? null;

        $this->logger->warning(\sprintf(
            'Worker stopping with status "%s"%s',
            $event->status,
            $reason ? \sprintf(' (%s)', $reason) : ''
        ));
    }
}
