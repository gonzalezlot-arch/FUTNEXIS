<?php

namespace App\Modules\Core\Notifications;

use App\Models\User;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
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
            ->subject('Restablece tu contraseña de FUTNEXIS')
            ->view(['core::mail.message', 'core::mail.message-text'], [
                'heading' => 'Restablece tu contraseña',
                'body' => 'Recibimos una solicitud para restablecer la contraseña de tu cuenta.',
                'actionText' => 'Crear una nueva contraseña',
                'actionUrl' => config('app.url').route('password.reset', [
                    'token' => $this->token,
                    'email' => $notifiable->email,
                ], false),
                'note' => 'El enlace vence en 60 minutos y solo puede usarse una vez. Si no lo solicitaste, ignora este mensaje: tu contraseña no cambiará.',
            ]);
    }
}
