<?php

namespace App\Channels;

use App\Services\HttpSmsClient;
use Illuminate\Notifications\Notification;

class HttpSmsChannel
{
    public function __construct(
        private HttpSmsClient $httpSms,
    ) {}

    /**
     * Envía la notificación dada.
     */
    public function send($notifiable, Notification $notification): void
    {
        if (! method_exists($notification, 'toHttpSms')) {
            return;
        }

        $message = $notification->toHttpSms($notifiable);
        $to = $notifiable->routeNotificationFor('sms', $notification);

        if (! $to || trim((string) $message) === '') {
            return;
        }

        $this->httpSms->send((string) $to, (string) $message);
    }
}
