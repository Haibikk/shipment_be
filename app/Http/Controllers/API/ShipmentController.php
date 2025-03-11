<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Models\Package;
use App\Models\TrackingUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ShipmentController extends Controller
{
    public function index()
    {
        $shipments = Shipment::with(['driver', 'packages'])->get();
        return response()->json(['data' => $shipments]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'driver_id' => 'required|exists:users,id',
            'package_ids' => 'required|array',
            'package_ids.*' => 'exists:packages,id',
        ]);

        $shipment = Shipment::create([
            'shipment_number' => 'SHP' . Str::upper(Str::random(8)),
            'driver_id' => $request->driver_id,
            'status' => 'pending',
        ]);

        $shipment->packages()->attach($request->package_ids);

        // Update packages status
        foreach ($request->package_ids as $packageId) {
            $package = Package::find($packageId);
            $package->update(['status' => 'processing']);
            
            TrackingUpdate::create([
                'package_id' => $packageId,
                'status' => 'Diproses',
                'description' => 'Paket telah dimasukkan ke dalam pengiriman',
            ]);
        }

        return response()->json(['message' => 'Shipment created successfully', 'data' => $shipment], 201);
    }

    public function show($id)
    {
        $shipment = Shipment::with(['driver', 'packages'])->findOrFail($id);
        return response()->json(['data' => $shipment]);
    }

    public function update(Request $request, $id)
    {
        $shipment = Shipment::findOrFail($id);
        
        $request->validate([
            'status' => 'sometimes|string|in:pending,in_progress,completed,cancelled',
        ]);

        if ($request->has('status')) {
            $oldStatus = $shipment->status;
            $newStatus = $request->status;
            
            $shipment->update(['status' => $newStatus]);
            
            if ($newStatus == 'in_progress' && $oldStatus == 'pending') {
                $shipment->update(['started_at' => now()]);
                
                // Update all packages in this shipment
                foreach ($shipment->packages as $package) {
                    $package->update(['status' => 'in_transit', 'picked_up_at' => now()]);
                    
                    TrackingUpdate::create([
                        'package_id' => $package->id,
                        'status' => 'Dalam Pengiriman',
                        'description' => 'Paket sedang dalam perjalanan',
                    ]);
                }
            } 
            elseif ($newStatus == 'completed' && $oldStatus != 'completed') {
                $shipment->update(['completed_at' => now()]);
                
                // Update all packages in this shipment
                foreach ($shipment->packages as $package) {
                    $package->update(['status' => 'delivered', 'delivered_at' => now()]);
                    
                    TrackingUpdate::create([
                        'package_id' => $package->id,
                        'status' => 'Terkirim',
                        'description' => 'Paket telah diterima oleh penerima',
                    ]);
                }
            }
        }

        return response()->json(['message' => 'Shipment updated successfully', 'data' => $shipment]);
    }

    public function destroy($id)
    {
        $shipment = Shipment::findOrFail($id);
        $shipment->packages()->detach();
        $shipment->delete();
        return response()->json(['message' => 'Shipment deleted successfully']);
    }
}
