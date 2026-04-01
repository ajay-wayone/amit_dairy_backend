<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // ✅ User ki sabhi notifications
    public function getNotifications(Request $request)
    {
        $user = $request->user();
        $notifications = $user->notifications()->orderBy('created_at', 'desc')->get();

        $unreadCount = $user->unreadNotifications()->count();
        $readCount = $notifications->count() - $unreadCount;

        return response()->json([
            'status' => true,
            'data' => $notifications,
            'unread_count' => $unreadCount,
            'read_count' => $readCount,
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
        $notification->update(['is_read' => true]);

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
        'count'  => $user->unreadNotifications()->count()
    ]);
}
}
