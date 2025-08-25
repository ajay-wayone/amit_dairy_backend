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

        return response()->json([
            'status' => true,
            'data'   => $user->notifications()->orderBy('created_at', 'desc')->get()
        ]);
    }

    // ✅ Single notification mark as read
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

        $notification->markAsRead();

        return response()->json([
            'status'  => true,
            'message' => 'Notification marked as read'
        ]);
    }

    // ✅ Unread notifications count
    public function unreadCount(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'status' => true,
            'count'  => $user->unreadNotifications()->count()
        ]);
    }
}
