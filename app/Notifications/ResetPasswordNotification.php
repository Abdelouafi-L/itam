<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    /**
     * The password reset token.
     * Raw PHP equivalent: your random token stored in session/DB
     */
    public function __construct(public string $token)
    {
        //
    }

    /**
     * Deliver via email only.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Build the reset password email.
     * Raw PHP equivalent: your reset-email.html template + PHPMailer
     */
    public function toMail(object $notifiable): MailMessage
    {
        // Build the reset URL with token + email
        // equivalent to your manual: APP_URL . '/reset-password?token=' . $token
        $resetUrl = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->email,
        ], false));

        return (new MailMessage)
            ->subject('Réinitialisation de votre mot de passe — ITAM TechCorp')
            ->greeting('Bonjour ' . $notifiable->first_name . ',')
            ->line('Vous recevez cet email car une demande de réinitialisation 
                    de mot de passe a été effectuée pour votre compte.')
            ->action('Réinitialiser mon mot de passe', $resetUrl)
            ->line('Ce lien expirera dans **60 minutes**.')
            ->line('Si vous n\'avez pas demandé de réinitialisation, 
                    aucune action n\'est requise.')
            ->salutation('L\'équipe ITAM TechCorp');
    }

    /**
     * Array representation — not used for email notifications.
     */
    public function toArray(object $notifiable): array
    {
        return [];
    }
}