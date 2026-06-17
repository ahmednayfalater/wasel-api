<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Mail\InvoiceMail;
use App\Models\Invoice;
use App\Models\Provider;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class InvoiceController extends Controller
{
    public function myInvoices(Request $request)
    {
        $invoices = Invoice::whereHas('subscription', fn($q) => $q->where('user_id', $request->user()->id))
            ->with('subscription.generator', 'payment')
            ->get();

        return response()->json($invoices);
    }

    public function show(int $id)
    {
        $invoice = Invoice::with('subscription.generator', 'payment')->findOrFail($id);
        return response()->json($invoice);
    }

    public function store(Request $request)
    {
        $request->validate([
            'subscription_id'  => 'required|exists:subscriptions,id',
            'previous_reading' => 'required|numeric',
            'current_reading'  => 'required|numeric|gt:previous_reading',
            'due_date'         => 'required|date',
        ]);

        $provider     = Provider::where('user_id', $request->user()->id)->firstOrFail();
        $subscription = Subscription::where('id', $request->subscription_id)
            ->whereHas('generator', fn($q) => $q->where('provider_id', $provider->id))
            ->firstOrFail();

        $amount = ($request->current_reading - $request->previous_reading) * $provider->price_KW;

        $invoice = Invoice::create([
            'subscription_id'  => $subscription->id,
            'previous_reading' => $request->previous_reading,
            'current_reading'  => $request->current_reading,
            'amount'           => $amount,
            'release_date'     => now(),
            'due_date'         => $request->due_date,
            'status'           => 'unpaid',
        ]);

        Mail::to($subscription->user->email)->queue(new InvoiceMail($invoice));

        return response()->json($invoice, 201);
    }

    public function providerInvoices(Request $request)
    {
        $provider = Provider::where('user_id', $request->user()->id)->firstOrFail();

        $invoices = Invoice::whereHas('subscription.generator', fn($q) => $q->where('provider_id', $provider->id))
            ->with('subscription.user', 'payment')
            ->get();

        return response()->json($invoices);
    }
}
