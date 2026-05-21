<?php

namespace App\Notifications;

use App\Channels\HttpSmsChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Notificación de prueba para httpSMS (tinker / tests manuales).
 *
 * User::find(1)?->notify(new TestSmsNotification());
 */
class TestSmsNotification extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return [HttpSmsChannel::class];
    }

    public function toHttpSms(object $notifiable): string
    {
        return 'Hola, este es un SMS automático enviado desde Laravel usando httpSMS.';
    }
}
