<?php
declare(strict_types=1);

namespace EonX\EasyCore\Bridge\Symfony\Messenger;

use EonX\EasyCore\Lock\ProcessWithLockTrait;
use EonX\EasyCore\Lock\WithLockDataInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Stamp\ConsumedByWorkerStamp;

final class ProcessWithLockMiddleware implements MiddlewareInterface
{
    use ProcessWithLockTrait;

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        if ($this->shouldSkip($envelope)) {
            return $stack->next()->handle($envelope, $stack);
        }

        /** @var \EonX\EasyCore\Lock\WithLockDataInterface $message */
        $message = $envelope->getMessage();

        $newEnvelope = $this->processWithLock($message, static function () use ($envelope, $stack): Envelope {
            return $stack->next()->handle($envelope, $stack);
        });

        return $newEnvelope ?? $envelope;
    }

    private function shouldSkip(Envelope $envelope): bool
    {
        if ($envelope->last(ConsumedByWorkerStamp::class) === null) {
            return true;
        }

        if ($envelope->getMessage() instanceof WithLockDataInterface === false) {
            return true;
        }

        return false;
    }
}
