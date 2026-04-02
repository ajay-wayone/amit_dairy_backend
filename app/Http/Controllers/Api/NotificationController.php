<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function getNotifications(Request $request)
    {
        $user = $request->user();
        $notifications = $user->notifications()->orderBy('created_at', 'desc')->paginate(10);

        $unreadCount = $user->notifications()->where('is_read', 0)->count();
        $readCount = $user->notifications()->where('is_read', 1)->count();

        return response()->json([
            'status' => true,
            'message' => 'Notifications retrieved successfully',
            'data' => [
                'notifications' => $notifications->items(),
                'unread_count' => $unreadCount,
                'read_count' => $readCount,
                'pagination' => [
                    'current_page' => $notifications->currentPage(),
                    'last_page' => $notifications->lastPage(),
                    'per_page' => $notifications->perPage(),
                    'total' => $notifications->total(),
                ]
            ]
        ]);
    }

    public function markAsRead(Request $request, $id)
    {
        $user = $request->user();
        $notification = $user->notifications()->where('id', $id)->first();

        if (!$notification) {
            return response()->json([
                'status'  => false,
                'message' => 'Notification not found'
            ], 404);
        }

        // ✅ Mark as read in Laravel and update custom is_read column
        $notification->markAsRead();
        $notification->update(['is_read' => 1]);

        return response()->json([
            'status'  => true,
            'message' => 'Notification marked as read'
        ]);
    }

    public function unreadCount(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'status' => true,
            'count'  => $user->notifications()->where('is_read', 0)->count()
        ]);
    }

    public function markAllAsRead(Request $request)
    {
        $user = $request->user();
        $user->unreadNotifications->markAsRead();

        // ✅ Update custom is_read column for all notifications
        $user->notifications()->where('is_read', 0)->update(['is_read' => 1]);

        return response()->json([
            'status'  => true,
            'message' => 'All notifications marked as read'
        ]);
    }
}
