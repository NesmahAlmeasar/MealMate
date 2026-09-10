<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display notifications page
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = $user->notifications()
            ->due()
            ->orderBy('created_at', 'desc');

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $notifications = $query->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Check for unread notifications (JSON for polling)
     */
    public function check(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Unauthenticated'], 401);
        }

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
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);

        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->markAsRead();

        return back()->with('success', 'تم تحديد الإشعار كمقروء');
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        Auth::user()->notifications()
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return back()->with('success', 'تم تحديد جميع الإشعارات كمقروءة');
    }

    /**
     * Store a scheduled notification/reminder.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,user_id',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'scheduled_for' => 'required|date|after:now',
        ]);

        // Access Service manually or inject it. 
        // Since it's not injected in constructor in the existing file (I need to check imports), 
        // I will use the service directly or via app().
        $service = app(\App\Services\NotificationService::class);
        
        $service->createCustomReminder(
            $request->user_id,
            $request->title,
            $request->message,
            $request->scheduled_for
        );

        return response()->json(['success' => true, 'message' => 'تم جدولة الإشعار بنجاح']);
    }
}
