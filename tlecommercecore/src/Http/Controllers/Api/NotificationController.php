<?php

namespace Plugin\TlcommerceCore\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Plugin\TlcommerceCore\Http\Resources\NotificationCollection;

class NotificationController extends Controller
{
    /**
     * Will return customer all notification
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function customerNotifications()
    {
        return [];
    }

    /**
     * Will update notification status as read
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead(Request $request)
    {
        try {
            $notification = auth('jwt-customer')->user()->unreadNotifications()->where('id', $request['id'])->first();

            if ($notification) {
                $notification->markAsRead();
                $unread_notification = new NotificationCollection(auth('jwt-customer')->user()->unreadNotifications);
                return response()->json(
                    [
                        'success' => true,
                        'unread_notification' => $unread_notification
                    ]
                );
            } else {
                return response()->json(
                    [
                        'success' => false
                    ]
                );
            }
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false
                ]
            );
        }
    }

    /**
     * Will update all notification status as read
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsReadAllNotification(Request $request)
    {
        try {
            auth('jwt-customer')->user()->unreadNotifications->markAsRead();
            return response()->json(
                [
                    'success' => true
                ]
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false
                ]
            );
        }
    }
}
