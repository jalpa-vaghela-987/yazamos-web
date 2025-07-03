<?php

// app/Mail/ProjectInvitationMail.php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProjectInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $projectNames;

    public function __construct($user, $projectNames)
    {
        $this->user = $user;
        $this->projectNames = $projectNames;
    }

    public function build()
    {
        return $this->subject('Project Invitation')
                    ->view('emails.project_invitation');
    }
}

