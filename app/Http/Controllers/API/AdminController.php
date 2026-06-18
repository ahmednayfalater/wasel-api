<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Invoice;
use App\Models\Provider;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function users()
    {
        return response()->json(User::all());
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->update($request->only(['first_name', 'second_name', 'last_name', 'email', 'phone', 'address']));
        return response()->json($user);
    }

    public function deleteUser($id)
    {
        User::findOrFail($id)->delete();
        return response()->json(['message' => 'تم حذف المستخدم']);
    }

    public function providers()
    {
        return response()->json(Provider::with('user', 'proofs')->get());
    }

    public function approveProvider($id)
    {
        $provider = Provider::findOrFail($id);
        $provider->update(['status' => 'active']);
        return response()->json(['message' => 'تم قبول مزود الخدمة']);
    }

    public function suspendProvider($id)
    {
        $provider = Provider::findOrFail($id);
        $provider->update(['status' => 'suspended']);
        return response()->json(['message' => 'تم تعليق مزود الخدمة']);
    }

    public function complaints()
    {
        return response()->json(Complaint::with('user')->get());
    }

    public function updateComplaint(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:pending,in_progress,closed']);
        $complaint = Complaint::findOrFail($id);
        $complaint->update(['status' => $request->status]);
        return response()->json($complaint);
    }

    public function systemReports()
    {
        return response()->json([
            'total_users'         => User::count(),
            'total_providers'     => Provider::count(),
            'active_providers'    => Provider::where('status', 'active')->count(),
            'pending_providers'   => Provider::where('status', 'pending')->count(),
            'total_subscriptions' => Subscription::count(),
            'active_subscriptions'=> Subscription::where('status', 'active')->count(),
            'total_invoices'      => Invoice::count(),
            'total_revenue'       => Invoice::where('status', 'paid')->sum('amount'),
            'total_complaints'    => Complaint::count(),
            'open_complaints'     => Complaint::where('status', '!=', 'closed')->count(),
        ]);
    }
}
