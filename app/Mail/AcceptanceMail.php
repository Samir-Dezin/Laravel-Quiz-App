<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AcceptanceMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $setupLink;

    /**
     * Create a new message instance.
     *
     * @param User $user
     * @param string $setupLink
     */
    public function __construct(User $user, $setupLink)
    {
        $this->user = $user;
        $this->setupLink = $setupLink;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Set Up Your Account')
                    ->view('acceptenceMail')
                    ->with([
                        'user' => $this->user,
                        'setupLink' => $this->setupLink,
                    ]);
    }
}
