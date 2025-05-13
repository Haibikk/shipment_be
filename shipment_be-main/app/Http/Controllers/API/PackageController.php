<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Package;
use App\Models\TrackingUpdate;
use Illuminate\Support\Str;

class PackageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $packages = Package::with(['sender', 'receiver'])->get();
        return response()->json(['data' => $packages]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'sender_id' => 'required|exists:users,id',
            'receiver_id' => 'required|exists:users,id',
            'item_name' => 'required|string',
            'weight' => 'required|numeric|min:0.01',
            'origin_address' => 'required|string',
            'destination_address' => 'required|string',
        ]);

        $package = Package::create([
            'tracking_number' => 'PKG' . Str::upper(Str::random(8)),
            'sender_id' => $request->sender_id,
            'receiver_id' => $request->receiver_id,
            'item_name' => $request->item_name,
            'description' => $request->description,
            'weight' => $request->weight,
            'length' => $request->length,
            'width' => $request->width,
            'height' => $request->height,
            'value' => $request->value,
            'origin_address' => $request->origin_address,
            'destination_address' => $request->destination_address,
            'status' => 'pending',
        ]);

        TrackingUpdate::create([
            'package_id' => $package->id,
            'status' => 'Paket terdaftar',
            'description' => 'Paket berhasil didaftarkan dalam sistem',
        ]);

        return response()->json(['message' => 'Package created successfully', 'data' => $package], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $package = Package::with(['sender', 'receiver', 'trackingUpdates', 'shipments'])->findOrFail($id);
        return response()->json(['data' => $package]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $package = Package::findOrFail($id);
        
        $request->validate([
            'item_name' => 'sometimes|string',
            'weight' => 'sometimes|numeric|min:0.01',
            'status' => 'sometimes|string|in:pending,processing,in_transit,delivered,cancelled',
        ]);

        $package->update($request->all());
        
        if ($request->has('status') && $request->status != $package->getOriginal('status')) {
            TrackingUpdate::create([
                'package_id' => $package->id,
                'status' => $request->status,
                'description' => 'Status paket berubah menjadi ' . $request->status,
            ]);
            
            if ($request->status == 'delivered') {
                $package->update(['delivered_at' => now()]);
            }
        }

        return response()->json(['message' => 'Package updated successfully', 'data' => $package]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $package = Package::findOrFail($id);
        $package->delete();
        return response()->json(['message' => 'Package deleted successfully']);
    }

    public function track($trackingNumber)
    {
        $package = Package::where('tracking_number', $trackingNumber)
            ->with(['trackingUpdates' => function($query) {
                $query->orderBy('created_at', 'desc');
            }])
            ->firstOrFail();
            
        return response()->json(['data' => $package]);
    }
}
