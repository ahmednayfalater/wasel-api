<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Provider;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    public function index()
    {
        return response()->json(Area::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|unique:areas',
            'coordinates' => 'nullable|string',
        ]);

        $area = Area::create($request->only(['name', 'coordinates']));

        $provider = Provider::where('user_id', $request->user()->id)->firstOrFail();
        $provider->areas()->attach($area->id);

        return response()->json($area, 201);
    }

    public function join(Request $request, int $id)
    {
        $provider = Provider::where('user_id', $request->user()->id)->firstOrFail();
        $area     = Area::findOrFail($id);

        if ($provider->areas()->where('area_id', $id)->exists()) {
            return response()->json(['message' => 'أنت مسجل في هذه المنطقة مسبقاً'], 422);
        }

        $provider->areas()->attach($area->id);

        return response()->json(['message' => 'تمت إضافة المنطقة بنجاح']);
    }
}
