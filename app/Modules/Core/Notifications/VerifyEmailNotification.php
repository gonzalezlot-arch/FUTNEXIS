<?php

namespace App\Modules\Core\Notifications;

use App\Models\User;
use App\Modules\Core\Support\AppUrl;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyEmailNotification extends Notification
{
    public function __construct(#[\SensitiveParameter] public string $token) {}

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
            ->subject('Activa tu cuenta de FUTNEXIS')
            ->view(['core::mail.message', 'core::mail.message-text'], [
                'heading' => 'Activa tu cuenta',
                'body' => 'Confirma que este correo es tuyo y crea tu contraseña para empezar a usar FUTNEXIS.',
                'actionText' => 'Activar mi cuenta',
                'actionUrl' => AppUrl::route('verification.show', ['token' => $this->token]),
                'note' => 'El enlace vence en 24 horas y solo puede usarse una vez. Si no solicitaste esta cuenta, ignora este mensaje.',
            ]);
    }
}
