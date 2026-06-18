<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Generator;
use App\Models\Provider;
use Illuminate\Http\Request;

class GeneratorController extends Controller
{
    public function index(Request $request)
    {
        $query = Generator::with('provider.user', 'provider.areas')
            ->whereHas('provider', fn($q) => $q->where('status', 'active'));

        if ($request->area_id) {
            $query->whereHas('provider.areas', fn($q) => $q->where('areas.id', $request->area_id));
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        return response()->json($query->paginate(15));
    }

    public function show(int $id)
    {
        $generator = Generator::with('provider.user', 'provider.areas')->findOrFail($id);
        return response()->json($generator);
    }

    public function search(Request $request)
    {
        $request->validate(['q' => 'required|string']);

        $generators = Generator::with('provider.user')
            ->whereHas('provider', fn($q) => $q->where('status', 'active'))
            ->where(function ($q) use ($request) {
                $q->whereHas('provider', fn($inner) => $inner->where('company_name', 'like', '%' . $request->q . '%'))
                  ->orWhere('type', 'like', '%' . $request->q . '%');
            })
            ->get();

        return response()->json($generators);
    }

    public function compare(Request $request)
    {
        $request->validate(['ids' => 'required|array|min:2|max:3', 'ids.*' => 'exists:generators,id']);

        $generators = Generator::with('provider.user', 'provider.areas')
            ->whereIn('id', $request->ids)
            ->get();

        return response()->json($generators);
    }

    public function myGenerators(Request $request)
    {
        $provider   = Provider::where('user_id', $request->user()->id)->firstOrFail();
        $generators = Generator::where('provider_id', $provider->id)->get();
        return response()->json($generators);
    }

    public function store(Request $request)
    {
        $request->validate([
            'type'    => 'required|string',
            'status'  => 'required|in:active,offline,maintenance',
            'gps'     => 'nullable|string',
            'powerKW' => 'required|numeric',
        ]);

        $provider = Provider::where('user_id', $request->user()->id)->firstOrFail();

        $generator = Generator::create([
            'provider_id' => $provider->id,
            'type'        => $request->type,
            'status'      => $request->status,
            'gps'         => $request->gps,
            'powerKW'     => $request->powerKW,
        ]);

        return response()->json($generator, 201);
    }

    public function update(Request $request, int $id)
    {
        $provider  = Provider::where('user_id', $request->user()->id)->firstOrFail();
        $generator = Generator::where('id', $id)->where('provider_id', $provider->id)->firstOrFail();

        $generator->update($request->only(['type', 'status', 'gps', 'powerKW']));

        return response()->json($generator);
    }

    public function destroy(Request $request, int $id)
    {
        $provider  = Provider::where('user_id', $request->user()->id)->firstOrFail();
        $generator = Generator::where('id', $id)->where('provider_id', $provider->id)->firstOrFail();

        $generator->delete();

        return response()->json(['message' => 'تم حذف المولد']);
    }
}
