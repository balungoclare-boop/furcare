<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentStatusNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Appointment $appointment,
        private readonly string $status
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase(object $notifiable): array
    {
        $statusLabel = $this->status === Appointment::STATUS_REJECTED ? 'rejected' : 'cancelled';

        return [
            'type' => 'appointment_status',
            'appointment_id' => $this->appointment->id,
            'status' => $this->status,
            'message' => sprintf(
                'Your %s appointment for %s was %s by the clinic.',
                strtolower($this->appointment->type_label),
                $this->appointment->display_pet_name,
                $statusLabel
            ),
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $statusLabel = $this->status === Appointment::STATUS_REJECTED ? 'rejected' : 'cancelled';

        return (new MailMessage)
            ->subject('Your appointment was '.$statusLabel)
            ->greeting('Hello '.$notifiable->name.',')
            ->line(sprintf(
                'Your %s appointment for %s was %s by the clinic.',
                strtolower($this->appointment->type_label),
                $this->appointment->display_pet_name,
                $statusLabel
            ))
            ->line('Date: '.optional($this->appointment->appointment_date)->format('F j, Y'))
            ->line('Please contact the clinic if you have questions or need to schedule another visit.');
    }
}
