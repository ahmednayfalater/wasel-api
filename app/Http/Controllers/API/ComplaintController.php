<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string',
            'description' => 'required|string',
            'target_type' => 'required|in:provider,system',
            'target_id'   => 'nullable|exists:providers,id',
        ]);

        $complaint = Complaint::create([
            'user_id'     => $request->user()->id,
            'title'       => $request->title,
            'description' => $request->description,
            'target_type' => $request->target_type,
            'target_id'   => $request->target_id,
            'status'      => 'pending',
        ]);

        return response()->json($complaint, 201);
    }
}
