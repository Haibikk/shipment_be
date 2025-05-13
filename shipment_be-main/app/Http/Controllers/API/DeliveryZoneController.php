<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DeliveryZone;
use Illuminate\Http\Request;

class DeliveryZoneController extends Controller
{
    public function index()
    {
        $zones = DeliveryZone::all();
        return response()->json(['data' => $zones]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:delivery_zones',
            'description' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
        ]);

        $zone = DeliveryZone::create($request->all());

        return response()->json(['message' => 'Delivery zone created successfully', 'data' => $zone], 201);
    }

    public function show($id)
    {
        $zone = DeliveryZone::findOrFail($id);
        return response()->json(['data' => $zone]);
    }

    public function update(Request $request, $id)
    {
        $zone = DeliveryZone::findOrFail($id);
        
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'code' => 'sometimes|string|max:10|unique:delivery_zones,code,' . $id,
            'description' => 'nullable|string',
            'base_price' => 'sometimes|numeric|min:0',
        ]);

        $zone->update($request->all());

        return response()->json(['message' => 'Delivery zone updated successfully', 'data' => $zone]);
    }

    public function destroy($id)
    {
        $zone = DeliveryZone::findOrFail($id);
        $zone->delete();
        return response()->json(['message' => 'Delivery zone deleted successfully']);
    }
}
