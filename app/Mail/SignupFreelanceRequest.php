<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SignupFreelanceRequest extends Mailable
{
    use Queueable, SerializesModels;
    public $freelance;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($freelance)
    {
        $this->freelance = $freelance;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.signupFreelance')->subject('Création de votre compte Playar.io');
    }
}
