<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomerResetPassword extends Notification
{
    public function __construct(protected string $url)
    {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Redefinição de Senha')
            ->line('Você solicitou a redefinição da sua senha.')
            ->action('Redefinir Senha', $this->url)
            ->line('Este link expira em 60 minutos.')
            ->line('Se você não solicitou isso, pode ignorar este e-mail.');
    }
}
