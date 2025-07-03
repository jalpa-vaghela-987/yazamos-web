<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $email;
    public $role;

    public function __construct(string $email,string $role) 
    {
        $this->email = $email;
        $this->role = $role;
    }

    public function build(): static
    {
        return $this->subject('Your Account is Ready - 2FA Required')
            ->view('emails.welcome-mail');
    }
}
