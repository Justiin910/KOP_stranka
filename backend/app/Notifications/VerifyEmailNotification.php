<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailBase;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmailNotification extends VerifyEmailBase
{
    /**
     * Build the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject(__('messages.email.verify_email.subject'))
            ->greeting(__('messages.email.verify_email.greeting'))
            ->line(__('messages.email.verify_email.intro'))
            ->action(__('messages.email.verify_email.action'), $verificationUrl)
            ->line(__('messages.email.verify_email.outro'));
    }
}
