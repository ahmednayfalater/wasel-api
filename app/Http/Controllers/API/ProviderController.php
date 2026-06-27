<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Provider;
use App\Models\Subscription;
use Illuminate\Http\Request;

class ProviderController extends Controller
{
    public function profile(Request $request)
    {
        $provider = Provider::with('user', 'areas', 'generators')->where('user_id', $request->user()->id)->firstOrFail();
        return response()->json($provider);
    }

    public function updateProfile(Request $request)
    {
        $provider = Provider::where('user_id', $request->user()->id)->firstOrFail();

        $provider->update($request->only(['company_name', 'price_KW', 'terms_subscr', 'accept_subscript']));

        if ($request->has('area_ids')) {
            $provider->areas()->sync($request->area_ids);
        }

        return response()->json($provider);
    }

    public function subscribers(Request $request)
    {
        $provider = Provider::where('user_id', $request->user()->id)->firstOrFail();

        $subscriptions = Subscription::whereHas('generator', fn($q) => $q->where('provider_id', $provider->id))
            ->with('user', 'generator')
            ->get();

        return response()->json($subscriptions);
    }

    public function revenueReports(Request $request)
    {
        $provider = Provider::where('user_id', $request->user()->id)->firstOrFail();

        $invoices = Invoice::whereHas('subscription.generator', fn($q) => $q->where('provider_id', $provider->id))
            ->where('status', 'paid')
            ->selectRaw('EXTRACT(MONTH FROM release_date) as month, EXTRACT(YEAR FROM release_date) as year, SUM(amount) as total')
            ->groupBy('year', 'month')
            ->orderByRaw('year desc, month desc')
            ->get();

        return response()->json($invoices);
    }
}
