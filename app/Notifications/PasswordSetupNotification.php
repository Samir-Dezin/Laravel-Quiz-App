<?php

namespace App\Notifications;

use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class PasswordSetupNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
   public function toMail($notifiable) {
    $token = Str::random(60);
    DB::table('password_resets')->insert([
        'email' => $notifiable->email,
        'token' => $token,
        'created_at' => now(),
    ]);

    $url = url("/password/setup/{$token}");

    return (new MailMessage)
                ->line('You have been invited to set up a password.')
                ->action('Set Password', $url)
                ->line('This link will expire in 24 hours.');
}


    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
