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
}
