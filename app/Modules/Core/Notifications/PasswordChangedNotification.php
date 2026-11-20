<?php

namespace App\Modules\Core\Notifications;

use App\Models\User;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordChangedNotification extends Notification
{
    /**
     * @return array<int, string>
     */
    public function via(User $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(User $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Tu contraseña de FUTNEXIS cambió')
            ->view(['core::mail.message', 'core::mail.message-text'], [
                'heading' => 'Tu contraseña cambió',
                'body' => 'La contraseña de tu cuenta se actualizó y todas las sesiones abiertas se cerraron.',
                'note' => 'Si no fuiste tú, recupera tu contraseña de inmediato y contacta al soporte de FUTNEXIS.',
            ]);
    }
}
