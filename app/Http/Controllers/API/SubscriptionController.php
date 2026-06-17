<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Mail\SubscriptionStatusMail;
use App\Models\Provider;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SubscriptionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate(['generator_id' => 'required|exists:generators,id']);

        $existing = Subscription::where('user_id', $request->user()->id)
            ->where('generator_id', $request->generator_id)
            ->whereIn('status', ['pending', 'active'])
            ->first();

        if ($existing) {
            return response()->json(['message' => 'لديك اشتراك قائم في هذا المولد'], 422);
        }

        $subscription = Subscription::create([
            'user_id'      => $request->user()->id,
            'generator_id' => $request->generator_id,
            'status'       => 'pending',
            'start_date'   => now(),
        ]);

        return response()->json($subscription, 201);
    }

    public function mySubscriptions(Request $request)
    {
        $subscriptions = Subscription::with('generator.provider.user')
            ->where('user_id', $request->user()->id)
            ->get();

        return response()->json($subscriptions);
    }

    public function cancel(Request $request, int $id)
    {
        $subscription = Subscription::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $subscription->update(['status' => 'cancelled']);

        return response()->json(['message' => 'تم إلغاء الاشتراك']);
    }

    public function approve(Request $request, int $id)
    {
        $provider     = Provider::where('user_id', $request->user()->id)->firstOrFail();
        $subscription = Subscription::whereHas('generator', fn($q) => $q->where('provider_id', $provider->id))
            ->where('id', $id)
            ->firstOrFail();

        $subscription->update(['status' => 'active']);

        Mail::to($subscription->user->email)->queue(
            new SubscriptionStatusMail('active', $subscription->generator->type)
        );

        return response()->json(['message' => 'تم قبول الاشتراك']);
    }

    public function reject(Request $request, int $id)
    {
        $provider     = Provider::where('user_id', $request->user()->id)->firstOrFail();
        $subscription = Subscription::whereHas('generator', fn($q) => $q->where('provider_id', $provider->id))
            ->where('id', $id)
            ->firstOrFail();

        $subscription->update(['status' => 'cancelled']);

        Mail::to($subscription->user->email)->queue(
            new SubscriptionStatusMail('cancelled', $subscription->generator->type)
        );

        return response()->json(['message' => 'تم رفض الاشتراك']);
    }
}
