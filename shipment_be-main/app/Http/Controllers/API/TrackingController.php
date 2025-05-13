<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\TrackingUpdate;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function addUpdate(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:packages,id',
            'status' => 'required|string',
            'location' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $update = TrackingUpdate::create($request->all());

        return response()->json(['message' => 'Tracking update added', 'data' => $update], 201);
    }

    public function trackPackage($trackingNumber)
    {
        $package = Package::where('tracking_number', $trackingNumber)
            ->with(['trackingUpdates' => function($query) {
                $query->orderBy('created_at', 'desc');
            }])
            ->firstOrFail();
            
        return response()->json(['data' => $package]);
    }
}
