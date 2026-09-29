<?php

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendRegistrationVerificationJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public array $backoff = [10, 30, 60];

    public function __construct(
        private readonly int $userId
    ) {
        $this->onQueue('users.notifications.verify');
    }

    public function handle(
        UserNotificationService $notificationService
    ): void {
        $user = User::query()->find($this->userId);

        if (!$user) {
            return;
        }

        $notificationService->sendEmailVerification($user);
    }
}
