<?php

namespace App\Http\Controllers;

use App\Models\Notifications;
use Illuminate\Http\Request;

class NotificationsController extends Controller
{
    public function index()
    {
        $notifications = Notifications::all();
        return response()->json($notifications, 200);
    }


    public function deleteNotification($notification_id)
    {

        $notification = Notifications::find($notification_id);
        $notification->delete();

        return response()->json("deleted", 200);

    }
}
