<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->hasRole('Admin')) {
            return view('admin.messages');
        } elseif ($user->hasRole('Specialist') || $user->hasRole('Nutrition Manager')) {
            return view('specialist.messages');
        } else {
            // Clients or unauthorized users shouldn't access this view directly in this phase
            // But we can redirect or show 403
            return abort(403, 'Unauthorized access to chat interface.');
        }
    }

    public function getConversations()
    {
        $userId = Auth::id();
        $currentUser = Auth::user();

        $users = collect();

        // 1. Admin: See ALL users (except self)
        if ($currentUser->hasRole('Admin')) {
            $users = User::where('user_id', '!=', $userId)
                ->with(['sentMessages' => function ($q) use ($userId) {
                    $q->where('receiver_id', $userId)->latest()->limit(1);
                }])
                ->get();
        }

        // 2. Specialist / Nutrition Manager
        // Can see users who:
        // a) Have sent a message TO them.
        // b) Are clients in an ACTIVE consultation with them.
        elseif ($currentUser->hasRole('Specialist') || $currentUser->hasRole('Nutrition Manager')) {
            $users = User::where('user_id', '!=', $userId)
                ->where(function ($query) use ($userId) {
                    // a) Has sent a message to me
                    $query->whereHas('sentMessages', function ($q) use ($userId) {
                        $q->where('receiver_id', $userId);
                    })
                    // b) OR has active consultation with me
                        ->orWhereHas('client', function ($q) use ($userId) {
                            $q->whereHas('consultations', function ($cq) use ($userId) {
                                $cq->where('nutritionist_id', $userId)
                                    ->where('status', 'active')
                                    ->where('end_time', '>', now());
                            });
                        });
                })
                ->with(['sentMessages' => function ($q) use ($userId) {
                    $q->where('receiver_id', $userId)->latest()->limit(1);
                }])
                ->get();
        }

        // 3. Restaurant Manager
        // Can ONLY see users who have sent a message TO them.
        elseif ($currentUser->hasRole('Restaurant Manager')) {
            $users = User::where('user_id', '!=', $userId)
                ->whereHas('sentMessages', function ($q) use ($userId) {
                    $q->where('receiver_id', $userId);
                })
                ->with(['sentMessages' => function ($q) use ($userId) {
                    $q->where('receiver_id', $userId)->latest()->limit(1);
                }])
                ->get();
        }

        // 4. Client (if they access via API)
        // Can see nutritionist they have active consultation with
        else {
            $users = User::where('user_id', '!=', $userId)
                ->whereHas('nutritionist', function ($q) use ($userId) { // Assuming User has nutritionist relationship or through Consultation
                    // Actually User doesn't have 'nutritionist' relation, we check consultations
                    $q->whereHas('consultations', function ($cq) use ($userId) {
                        $cq->where('client_id', $userId)
                            ->where('status', 'active')
                            ->where('end_time', '>', now());
                    });
                })
                ->with(['sentMessages' => function ($q) use ($userId) {
                    $q->where('receiver_id', $userId)->latest()->limit(1);
                }])
                ->get();
        }

        // Processing users to add last_message and unread_count
        // We use eager loaded sentMessages but we also need messages SENT by me to them to find the TRUE last message
        // Optimization: We can't easily eager load the "last message between us" efficiently for all at once without subqueries.
        // For 50-100 users, loop with optimized query is acceptable, or use a window function approach (complex in Eloquent).
        // Let's use the subquery approach or just the loop since it's cleaner for now and we added indexes.

        $users = $users->map(function ($user) use ($userId) {
            $lastMessage = Message::betweenUsers($userId, $user->user_id)
                ->latest()
                ->first();

            $unreadCount = Message::where('sender_id', $user->user_id)
                ->where('receiver_id', $userId)
                ->where('is_read', false)
                ->count();

            $user->last_message = $lastMessage;
            $user->unread_count = $unreadCount;
            // For sorting: verify if message exists, else use user creation time (or null to push to bottom)
            $user->last_activity = $lastMessage ? $lastMessage->created_at : null;

            return $user;
        });

        // Sort by last activity (most recent message first)
        // If no message, maybe put them at the bottom
        $users = $users->sortByDesc('last_activity')->values();

        return response()->json($users);
    }

    public function getMessages($otherUserId)
    {
        $myId = Auth::id();
        $currentUser = Auth::user();

        // Permission Check
        if (! $currentUser->canChatWith($otherUserId)) {
            return response()->json(['error' => 'You do not have permission to chat with this user.'], 403);
        }

        // Pagination: 50 messages per page
        $messages = Message::betweenUsers($myId, $otherUserId)
            ->orderBy('created_at', 'desc') // Get newest first for pagination, then reverse in frontend if needed
            ->paginate(50);

        // Mark as read (only those sent by otherUser to me)
        Message::where('sender_id', $otherUserId)
            ->where('receiver_id', $myId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        // Return reverse so frontend gets chronological order if it expects array,
        // but paginate returns object. Frontend usually handles preprending.
        // We will return the paginated object.
        return response()->json($messages);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,user_id',
            'message' => 'nullable|string|max:1000',
            'file' => [
                'nullable',
                'file',
                'max:5120', // 5MB
                'mimes:jpg,jpeg,png,pdf,doc,docx', // Strict extensions
            ],
        ]);

        if (! $request->message && ! $request->hasFile('file')) {
            return response()->json(['error' => 'Message or file is required'], 422);
        }

        $currentUser = Auth::user();
        $receiverId = $request->receiver_id;

        // Permission Check
        if (! $currentUser->canChatWith($receiverId)) {
            return response()->json([
                'error' => 'You do not have permission to send messages to this user.',
            ], 403);
        }

        $data = [
            'sender_id' => Auth::id(),
            'receiver_id' => $receiverId,
            'message' => $request->message,
            'is_read' => false,
        ];

        if ($request->hasFile('file')) {
            $file = $request->file('file');

            // Double check MIME type for security (though validation handles basics)
            $allowedMimes = ['image/jpeg', 'image/png', 'application/pdf',
                'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];

            if (! in_array($file->getMimeType(), $allowedMimes)) {
                return response()->json(['error' => 'Invalid file type'], 422);
            }

            // Secure filename: time + random + valid extension
            $filename = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
            $path = $file->storeAs('chat_files', $filename, 'public');

            $data['file_url'] = $path;

            // Determine type
            $mime = $file->getMimeType();
            if (str_starts_with($mime, 'image/')) {
                $data['file_type'] = 'image';
            } else {
                $data['file_type'] = 'document';
            }
        }

        $message = Message::create($data);

        // Notify Receiver
        try {
            $notificationService = app(\App\Services\NotificationService::class);
            $notificationService->notifyNewMessage($message);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send message notification: '.$e->getMessage());
        }

        return response()->json($message);
    }

    public function canSendMessage($userId)
    {
        $currentUser = Auth::user();

        if ($currentUser->canChatWith($userId)) {
            return response()->json([
                'can_send' => true,
                'message' => 'Authorized',
            ]);
        }

        return response()->json([
            'can_send' => false,
            'message' => 'No permission to send messages to this user',
        ]);
    }

    public function getUserDetails($id)
    {
        $user = User::findOrFail($id);

        return response()->json([
            'user_id' => $user->user_id,
            'Fname' => $user->Fname,
            'Lname' => $user->Lname,
            'photo_url' => $user->photo_url,
            // Add other fields if needed
        ]);
    }
}
