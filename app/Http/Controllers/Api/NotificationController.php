<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Get all notifications for authenticated user.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $notifications = $user->notifications()
            ->due()
            ->paginate(20);

        return response()->json([
            'status' => 'success',
            'data' => $notifications,
        ]);
    }

    /**
     * Get only unread notifications.
     */
    public function unread(Request $request)
    {
        $user = $request->user();
        $notifications = $user->notifications()
            ->due()
            ->unread()
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $notifications,
            'count' => $notifications->count(),
        ]);
    }

    /**
     * Mark specific notification as read.
     */
    public function markAsRead($id)
    {
        $notification = Notification::find($id);

        if (! $notification) {
            return response()->json(['status' => 'error', 'message' => 'Notification not found'], 404);
        }

        if ($notification->user_id !== Auth::id()) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $notification->markAsRead();

        return response()->json([
            'status' => 'success',
            'message' => 'Notification marked as read',
        ]);
    }

    /**
     * Mark all user notifications as read.
     */
    public function markAllAsRead(Request $request)
    {
        $user = $request->user();
        $user->notifications()->unread()->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'All notifications marked as read',
        ]);
    }

    /**
     * Delete a notification.
     */
    public function destroy($id)
    {
        $notification = Notification::find($id);

        if (! $notification) {
            return response()->json(['status' => 'error', 'message' => 'Notification not found'], 404);
        }

        if ($notification->user_id !== Auth::id()) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $notification->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Notification deleted',
        ]);
    }

    /**
     * Create a custom reminder.
     */
    public function createReminder(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'scheduled_for' => 'required|date|after:now',
        ]);

        $notification = $this->notificationService->createCustomReminder(
            Auth::id(),
            $request->title,
            $request->message,
            $request->scheduled_for
        );

        if ($notification) {
            return response()->json([
                'status' => 'success',
                'message' => 'Reminder set successfully',
                'data' => $notification,
            ]);
        }

        return response()->json(['status' => 'error', 'message' => 'Failed to create reminder'], 500);
    }
}
