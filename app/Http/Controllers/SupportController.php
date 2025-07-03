<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SupportController extends Controller
{
    public function sendSupportMessage(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'message' => 'required|string',
        ]);

        Mail::send('emails.support', [
            'name' => $request->name,
            'email' => $request->email,
            'messageContent' => $request->message,
        ], function ($message) use ($request) {
            $message->to('support@yazamos.com')
                ->subject('Support Message from ' . $request->name)
                ->replyTo($request->email);
        });

        return response()->json(['message' => 'Support message sent successfully.']);
    }
}
