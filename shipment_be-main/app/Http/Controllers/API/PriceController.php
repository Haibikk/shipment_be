<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Price;
use Illuminate\Http\Request;

class PriceController extends Controller
{
    public function index()
    {
        $prices = Price::with(['originZone', 'destinationZone'])->get();
        return response()->json(['data' => $prices]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'origin_zone_id' => 'required|exists:delivery_zones,id',
            'destination_zone_id' => 'required|exists:delivery_zones,id',
            'price_per_kg' => 'required|numeric|min:0',
            'base_price' => 'required|numeric|min:0',
        ]);

        // Check if already exists
        $exists = Price::where('origin_zone_id', $request->origin_zone_id)
            ->where('destination_zone_id', $request->destination_zone_id)
            ->exists();
            
        if ($exists) {
            return response()->json(['message' => 'Price for this route already exists'], 422);
        }

        $price = Price::create($request->all());

        return response()->json(['message' => 'Price created successfully', 'data' => $price], 201);
    }

    public function show($id)
    {
        $price = Price::with(['originZone', 'destinationZone'])->findOrFail($id);
        return response()->json(['data' => $price]);
    }

    public function update(Request $request, $id)
    {
        $price = Price::findOrFail($id);
        
        $request->validate([
            'price_per_kg' => 'sometimes|numeric|min:0',
            'base_price' => 'sometimes|numeric|min:0',
        ]);

        $price->update($request->all());

        return response()->json(['message' => 'Price updated successfully', 'data' => $price]);
    }

    public function destroy($id)
    {
        $price = Price::findOrFail($id);
        $price->delete();
        return response()->json(['message' => 'Price deleted successfully']);
    }

    public function calculateShippingCost(Request $request)
    {
        $request->validate([
            'origin_zone_id' => 'required|exists:delivery_zones,id',
            'destination_zone_id' => 'required|exists:delivery_zones,id',
            'weight' => 'required|numeric|min:0.01',
        ]);

        $price = Price::where('origin_zone_id', $request->origin_zone_id)
            ->where('destination_zone_id', $request->destination_zone_id)
            ->first();
            
        if (!$price) {
            return response()->json(['message' => 'No price found for this route'], 404);
        }

        $totalCost = $price->base_price + ($request->weight * $price->price_per_kg);

        return response()->json([
            'data' => [
                'base_price' => $price->base_price,
                'price_per_kg' => $price->price_per_kg,
                'weight' => $request->weight,
                'weight_cost' => $request->weight * $price->price_per_kg,
                'total_cost' => $totalCost
            ]
        ]);
    }
}
