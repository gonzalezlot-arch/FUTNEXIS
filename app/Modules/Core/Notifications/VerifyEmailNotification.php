<?php

namespace App\Modules\Core\Notifications;

use App\Models\User;
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
            ->subject('Confirma tu correo en FUTNEXIS')
            ->view(['core::mail.message', 'core::mail.message-text'], [
                'heading' => 'Confirma tu correo electrónico',
                'body' => 'Gracias por crear tu cuenta. Confirma tu dirección para poder iniciar sesión en FUTNEXIS.',
                'actionText' => 'Confirmar mi correo',
                'actionUrl' => config('app.url').route('verification.show', ['token' => $this->token], false),
                'note' => 'El enlace vence en 24 horas y solo puede usarse una vez. Si no creaste esta cuenta, ignora este mensaje.',
            ]);
    }
}
