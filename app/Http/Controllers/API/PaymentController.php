<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Mail\PaymentReviewMail;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Provider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PaymentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'invoice_id'    => 'required|exists:invoices,id',
            'amount'        => 'required|numeric',
            'receipt_image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $path = $request->file('receipt_image')->store('receipts', 'public');

        $payment = Payment::create([
            'invoice_id'     => $request->invoice_id,
            'amount'         => $request->amount,
            'receipt_image'  => $path,
            'invoice_review' => 'pending',
            'pay_date'       => now(),
        ]);

        Invoice::where('id', $request->invoice_id)->update(['status' => 'paid']);

        return response()->json($payment, 201);
    }

    public function myPayments(Request $request)
    {
        $payments = Payment::whereHas('invoice.subscription', fn($q) => $q->where('user_id', $request->user()->id))
            ->with('invoice')
            ->get();

        return response()->json($payments);
    }

    public function review(Request $request, int $id)
    {
        $request->validate(['status' => 'required|in:approved,rejected']);

        $provider = Provider::where('user_id', $request->user()->id)->firstOrFail();
        $payment  = Payment::whereHas('invoice.subscription.generator', fn($q) => $q->where('provider_id', $provider->id))
            ->where('id', $id)
            ->firstOrFail();

        $payment->update(['invoice_review' => $request->status]);

        if ($request->status === 'rejected') {
            $payment->invoice->update(['status' => 'unpaid']);
        }

        $customer = $payment->invoice->subscription->user;
        Mail::to($customer->email)->queue(new PaymentReviewMail($request->status, $payment->amount));

        return response()->json(['message' => 'تم تحديث حالة الدفع']);
    }
}
