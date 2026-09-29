<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmailNotification extends BaseVerifyEmail
{
    public function toMail($notifiable): MailMessage
    {
        $verificationUrl = $this->verificationUrl($notifiable);


        return (new MailMessage())
            ->subject('Подтверждение почты — Наш Магазин')
            ->view('emails.verify-email', [
                'url' => $verificationUrl,
                'user' => $notifiable
            ]);
    }
}
