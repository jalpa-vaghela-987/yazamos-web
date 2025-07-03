<?php
use App\Notifications\GenericNotification;

if (!function_exists('send_notification')) {
    function send_notification($user, $title, $body, $data = [])
    {
        $user->notify(new GenericNotification($title, $body, $data));
    }
}