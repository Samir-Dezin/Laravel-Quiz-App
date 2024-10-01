<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class PasswordSetupMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $token;

    public function __construct($user, $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    public function build()
    {
        // Generate the URL for password setup
        $url = URL::temporarySignedRoute(
            'password.setup', // This should match the named route in your web.php
            now()->addMinutes(30), // Set expiration for the URL
            ['token' => $this->token, 'email' => $this->user->email]
        );

        return $this->subject('Set Up Your Password')
            ->view('password')
            ->with(['url' => $url]); // Pass the URL to the view
    }
}
