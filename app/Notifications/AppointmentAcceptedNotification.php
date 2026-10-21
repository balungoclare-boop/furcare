<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentAcceptedNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Appointment $appointment
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $date = optional($this->appointment->appointment_date)->format('F j, Y');
        $time = $this->appointment->preferred_time
            ? date('g:i A', strtotime((string) $this->appointment->preferred_time))
            : 'Clinic scheduled time';

        return (new MailMessage)
            ->subject('Your appointment has been accepted')
            ->greeting('Hello '.$notifiable->name.',')
            ->line('Your '.$this->appointment->type_label.' appointment for '.$this->appointment->display_pet_name.' has been accepted by the clinic.')
            ->line('Date: '.$date)
            ->line('Time: '.$time)
            ->line('Purpose: '.$this->appointment->display_purpose)
            ->action('View Accepted Appointments', route('appointments.accepted'))
            ->line('Please contact the clinic if you need to reschedule.');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'appointment_accepted',
            'appointment_id' => $this->appointment->id,
            'status' => $this->appointment->status,
            'message' => sprintf(
                'Your %s appointment for %s has been accepted by the clinic.',
                strtolower($this->appointment->type_label),
                $this->appointment->display_pet_name
            ),
        ];
    }
}
