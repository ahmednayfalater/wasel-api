<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'target_type' => 'required|in:provider,system',
            'target_id'   => 'nullable|exists:providers,id',
            'rate'        => 'required|integer|min:1|max:5',
            'comment'     => 'nullable|string',
        ]);

        $review = Review::create([
            'user_id'     => $request->user()->id,
            'target_type' => $request->target_type,
            'target_id'   => $request->target_id,
            'rate'        => $request->rate,
            'comment'     => $request->comment,
        ]);

        return response()->json($review, 201);
    }

    public function providerReviews(Request $request)
    {
        $provider = Provider::where('user_id', $request->user()->id)->firstOrFail();

        $reviews = Review::where('target_type', 'provider')
            ->where('target_id', $provider->id)
            ->with('user')
            ->get();

        return response()->json([
            'reviews' => $reviews,
            'average' => $reviews->avg('rate'),
        ]);
    }
}
