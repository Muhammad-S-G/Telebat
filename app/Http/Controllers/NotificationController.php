<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{


    public function index(Request $request)
    {
        $user = $request->user();
        $notifications = $user->notifications()->get();

        return success(['notifications' => $notifications]);
    }



    public function markAsRead(Request $request,  $id)
    {
        $user = $request->user();
        $notification = $user->notifications()->findOrFail($id); // from type DatabaseNotification
        $notification->markAsRead();

        return success([], 200, 'Notification marked as read');
    }



    public function unread(Request $request)
    {
        $user = $request->user();
        $notifications = $user->unreadNotifications()->get();
        $count = $user->unreadNotifications()->count();
        return success(['results' => $count, 'notifications' => $notifications]);
    }


    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications()->update(['read_at' => now()]);
        return success(['message' => 'All notifications marked as read']);
    }
}
