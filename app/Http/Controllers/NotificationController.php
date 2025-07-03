<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Notifications\GenericNotification;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        return $request->user()->notifications()->latest()->get();
    }

    public function markAsRead($id,Request $request)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        if (!$notification) {
            return response()->json(['message' => 'Notification not found'], 404);
        }
        $notification->markAsRead();

        return response()->json(['message' => 'Marked as read']);
    }

    public function send(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string',
            'body' => 'required|string',
        ]);

        $user = User::find($request->user_id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        if ($user->id === $request->user()->id) {
            return response()->json(['message' => 'You cannot send a notification to yourself'], 400);
        }
        $user->notify(new GenericNotification($request->title, $request->body));

        return response()->json(['message' => 'Notification sent']);
    }
}
