<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Poster;
use Illuminate\Http\Request;

class PosterController extends Controller
{
    public function index()
    {
        return response()->json(Poster::with('user')->orderBy('created_at', 'desc')->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string',
            'description' => 'required|string',
        ]);

        $poster = Poster::create([
            'user_id'     => $request->user()->id,
            'title'       => $request->title,
            'description' => $request->description,
        ]);

        return response()->json($poster, 201);
    }

    public function update(Request $request, $id)
    {
        $poster = Poster::where('id', $id)->where('user_id', $request->user()->id)->firstOrFail();
        $poster->update($request->only(['title', 'description']));
        return response()->json($poster);
    }

    public function destroy(Request $request, $id)
    {
        $poster = Poster::where('id', $id)->where('user_id', $request->user()->id)->firstOrFail();
        $poster->delete();
        return response()->json(['message' => 'تم حذف الإعلان']);
    }
}
