<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\SendMessageRequest;
use App\Http\Resources\Mobile\MessageResource;
use App\Models\AssignedUserProject;
use App\Services\SMSService;
use App\Services\FirebaseNotificationService;
use Illuminate\Support\Facades\Log;

class MessageController extends Controller
{
    protected $smsService;
    protected $firebaseService;

    public function __construct(SMSService $smsService, FirebaseNotificationService $firebaseService)
    {
        $this->smsService = $smsService;
        $this->firebaseService = $firebaseService;
    }


    public function send(SendMessageRequest $request)
    {
        $validated = $request->validated();
        $customPath = null;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $timestamp = now()->format('Y/m/d/His');
            $originalName = $file->getClientOriginalName();
            $customPath = "messages/{$timestamp}_{$originalName}";
            $file->storeAs('', $customPath, 'public');
        }

        // Send SMS to direct receiver (if applicable)
        $response = [];


        // Store the message
        $message = Message::create([
            'sender_id'     => Auth::id(),
            'receiver_id'   => $request->receiver_id,
            'receiver_type' => $request->receiver_type,
            'subject'       => $request->subject,
            'message'       => $request->message,
            'project_id'    => $request->project_id ?? null,
            'phase_id'      => $request->phase_id ?? null,
            'file'          => $customPath,
            'response'      => json_encode($response)
        ]);

        if (!$message) {
            return response()->json([
                'status' => 'error',
                'status_code' => 500,
                'message' => 'Failed to send message.'
            ], 500);
        }
        $receiver = User::find($request->receiver_id);
        if ($receiver && $receiver->phone_number) {
            $this->firebaseService->sendToUser(
                $receiver->id,
                $request->subject,
                $request->message
            );
        } else {

            // Get users assigned to the project…
            $assignedUsers = AssignedUserProject::where('project_id', $request->project_id)
                ->with('user')
                ->get()
                ->pluck('user')
                ->filter(function ($user) use ($request) {
                    if (!$user) return false;

                    switch ($request->receiver_type) {
                        case 1: // Investor
                            return $user->hasRole('investor');
                        case 2: // Tenant
                            return $user->hasRole('tenant');
                        case 3: // Admin
                            return $user->hasRole('admin');
                        case 0: // All
                        default:
                            return true;
                    }
                })
                // **Remove the auth user**
                ->reject(function ($user) {
                    return $user->id === Auth::id();
                });

            // Send FCM to filtered users
            foreach ($assignedUsers as $user) {
                try {
                    $this->firebaseService->sendToUser(
                        $user->id,
                        $request->subject,
                        $request->message
                    );
                } catch (\Exception $e) {
                    Log::warning('FCM send failed', [
                        'user_id' => $user->id,
                        'error'   => $e->getMessage(),
                    ]);
                }
            }
        }



        $message->load(['sender', 'receiver', 'phase', 'project']);
        return MessageResource::make($message);
    }



    // Get chat messages between current user and another
    public function chatWith($userId)
    {
        $messages = Message::with(['sender', 'receiver', 'phase', 'project'])->where(function ($q) use ($userId) {
            $q->where('sender_id', Auth::id())
                ->where('receiver_id', $userId);
        })
            ->orWhere(function ($q) use ($userId) {
                $q->where('sender_id', $userId)
                    ->where('receiver_id', Auth::id());
            })
            ->orderBy('created_at', 'asc')
            ->get();
        if ($messages->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'status_code' => 404,
                'message' => 'No messages found for this conversation.'
            ], 404);
        }
        return MessageResource::collection($messages);
    }

    // Optional: List conversations (users you've chatted with)
    public function conversations()
    {
        $userId = Auth::id();

        $conversations = Message::with(['sender', 'receiver', 'phase', 'project'])->where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->with(['sender', 'receiver'])
            ->get()
            ->groupBy(function ($message) use ($userId) {
                return $message->sender_id == $userId ? $message->receiver_id : $message->sender_id;
            });
        if ($conversations->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'status_code' => 404,
                'message' => 'No conversations found.'
            ], 404);
        }
        return MessageResource::collection($conversations);
    }

    public function messages(Request $request, string $id)
    {
        $input = $request->all();

        $userRole = loginUser()->hasRole(ROLE_ENTREPRENEUR);
        if (isset($input['is_sender'])) {
            $userRole = Null;
        }

        $messages = Message::with(['sender', 'receiver', 'phase', 'project', 'sender.roles'])
            ->where('project_id', $id)
            ->when(isset($input['is_sender']), function ($query) use ($input) {
                $query->where('sender_id', loginUser()->id);
            })
            ->when(isset($input['receiver_type']), function ($query) use ($input, $userRole) {
                $query->where('receiver_type', $input['receiver_type']);
            })
            ->when($userRole, function ($query) use ($input) {
                $query->Where('receiver_id', loginUser()->id)
                    ->orWhereNull('receiver_type');

                $query->when(isset($input['receiver_type']), function ($mainQuery) use ($input) {
                    $role = Message::RECEIVER_TYPES[$input['receiver_type']] ?? null;
                    $mainQuery->whereHas('sender', function ($q) use ($role) {
                        $q->whereHas('roles', function ($subQuery) use ($role) {
                            $subQuery->where('name', $role);
                        });
                    });
                });
            })
            ->orderBy('created_at', 'desc')
            //            ->filterRecords($input)
            ->get();
        if ($messages->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'status_code' => 404,
                'message' => 'No messages found for this project.'
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Project messages get successfully.',
            'data' => [
                'messages' => MessageResource::collection($messages)
            ]
        ], 200);
    }

    public function AllMessages(Request $request)
    {
        $input = $request->all();

        $userRole = loginUser()->hasRole(ROLE_ENTREPRENEUR) || loginUser()->hasRole(ROLE_ADMIN);
        if (isset($input['is_sender'])) {
            $userRole = Null;
        }

        $messages = Message::with(['sender', 'receiver', 'phase', 'project', 'sender.roles'])
            ->when(isset($input['is_sender']), function ($query) {
                $query->where('sender_id', loginUser()->id);
            })
            ->when($userRole, function ($query) use ($input) {
                $query->Where('receiver_id', loginUser()->id)
                    ->orWhereNull('receiver_type');
            })
            ->orderBy('created_at', 'desc')
            ->get();
        if ($messages->isEmpty()) { 
            return response()->json([
                'status' => 'error',
                'status_code' => 404,
                'message' => 'No messages found.'
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Project messages get successfully.',
            'data' => [
                'messages' => MessageResource::collection($messages)
            ]
        ], 200);
    }
}
