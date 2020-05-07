<?php

declare(strict_types=1);

namespace EonX\EasyCore\Bridge\Laravel\Providers;

use EonX\EasyCore\Bridge\Laravel\Listeners\DoctrineClearEmBeforeJobListener;
use EonX\EasyCore\Bridge\Laravel\Listeners\DoctrineRestartQueueOnEmCloseListener;
use EonX\EasyCore\Bridge\Laravel\Listeners\QueueWorkerStoppingListener;
use Illuminate\Queue\Events\JobExceptionOccurred;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\Events\WorkerStopping;
use Illuminate\Support\ServiceProvider;

final class EasyCoreServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/easy-core.php' => \base_path('config/easy-core.php'),
        ]);

        $this->clearDoctrineEmBeforeJob();
        $this->logQueueWorkerStopping();
        $this->restartQueueOnEmClose();
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/easy-core.php', 'easy-core');
    }

    private function clearDoctrineEmBeforeJob(): void
    {
        if ((bool)\config('easy-core.clear_doctrine_em_before_job', false) === false) {
            return;
        }

        $this->app->get('events')->listen(JobProcessing::class, DoctrineClearEmBeforeJobListener::class);
    }

    private function logQueueWorkerStopping(): void
    {
        if ((bool)\config('easy-core.log_queue_worker_stop', true) === false) {
            return;
        }

        $this->app->get('events')->listen(WorkerStopping::class, QueueWorkerStoppingListener::class);
    }

    private function restartQueueOnEmClose(): void
    {
        if ((bool)\config('easy-core.restart_queue_on_doctrine_em_close', true) === false) {
            return;
        }

        $this->app->get('events')->listen(JobExceptionOccurred::class, DoctrineRestartQueueOnEmCloseListener::class);
    }
}
