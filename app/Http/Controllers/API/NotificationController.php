<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Mail\GeneralNotificationMail;
use App\Models\Notification;
use App\Models\Provider;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class NotificationController extends Controller
{
    public function myNotifications(Request $request)
    {
        $notifications = Notification::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($notifications);
    }

    public function markAsRead(Request $request, int $id)
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $notification->update(['is_read' => true]);

        return response()->json(['message' => 'تم تحديد الإشعار كمقروء']);
    }

    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'type'    => 'required|in:invoice,price_change,generator_status',
        ]);

        $provider = Provider::where('user_id', $request->user()->id)->firstOrFail();

        $subscribers = Subscription::whereHas('generator', fn($q) => $q->where('provider_id', $provider->id))
            ->where('status', 'active')
            ->with('user')
            ->get();

        foreach ($subscribers as $subscription) {
            Notification::create([
                'user_id' => $subscription->user_id,
                'message' => $request->message,
                'type'    => $request->type,
                'is_read' => false,
            ]);

            try {
                Mail::to($subscription->user->email)->send(
                    new GeneralNotificationMail($request->message, $request->type)
                );
            } catch (\Exception $e) {
                // Mail failure doesn't block in-app notification
            }
        }

        return response()->json(['message' => 'تم إرسال الإشعار لجميع المشتركين']);
    }
}
