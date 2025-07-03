<!-- resources/views/emails/project_invitation.blade.php -->

<p>Dear {{ $user->name }},</p>

<p>You have been invited to join the following projects:</p>

<p><strong>{{ $projectNames }}</strong></p>

<p>Please log in to your account to accept or reject these invitations.</p>

<p>Best regards,<br>Yazamos</p>
