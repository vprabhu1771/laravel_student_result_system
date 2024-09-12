<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use OneSignal;

class NotificationController extends Controller
{
    //
    public function sendNotification()
    {
        OneSignal::sendNotificationToAll(
            "2024 Sem Results Available Now. Thank You.",
            $url = null,
            $data = null,
            $buttons = null,
            $schedule = null
        );
        
        return response()->json(['success' => 'Notification sent successfully']);
    }

    public function sendToUser($playerId)
    {
        OneSignal::sendNotificationToUser(
            "Hello, this is a personalized notification!",
            $playerId,
            $url = null,
            $data = null,
            $buttons = null,
            $schedule = null
        );
        
        return response()->json(['success' => 'Notification sent to the user']);
    }
}