<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class LicenseExpiryNotification extends Notification
{
    use Queueable;

    /**
     * @param Collection $licenses — the expiring licenses
     */
    public function __construct(public Collection $licenses)
    {
        //
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Build the expiry notification email.
     * RF-20: Include software name, expiry date, seats.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('⚠️ Licences expirant bientôt — ITAM TechCorp')
            ->greeting('Bonjour ' . $notifiable->first_name . ',')
            ->line(
                $this->licenses->count() . ' licence(s) expire(nt) dans moins de 30 jours :'
            );

        // Add one line per expiring license
        foreach ($this->licenses as $license) {
            $mail->line(
                '• ' . ($license->software->product->name ?? 'N/A') .
                ' — Expire le : ' .
                $license->expiry_date->format('d/m/Y') .
                ' (' . $license->days_remaining . ' jour(s))' .
                ' — Sièges : ' . $license->seats_used .
                '/' . $license->seats_total
            );
        }

        return $mail
            ->action(
                'Gérer les licences',
                route('licenses.index')
            )
            ->line('Veuillez renouveler ces licences avant leur expiration.')
            ->salutation('L\'équipe ITAM TechCorp');
    }
}