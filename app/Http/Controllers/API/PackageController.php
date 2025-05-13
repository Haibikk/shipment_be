<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Package;
use App\Models\TrackingUpdate;
use Illuminate\Support\Str;

/**
 * @OA\Tag(
 *     name="Package",
 *     description="API untuk mengelola paket"
 * )
 */
class PackageController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/packages",
     *     summary="Mengambil semua paket",
     *     tags={"Package"},
     *     @OA\Response(
     *         response=200,
     *         description="Daftar paket berhasil diambil",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Package"))
     *         )
     *     )
     * )
     */
    public function index()
    {
        $packages = Package::with(['shipment', 'trackingUpdates'])->get();
        return response()->json(['data' => $packages]);
    }

    /**
     * @OA\Post(
     *     path="/api/packages",
     *     summary="Menambahkan paket baru",
     *     tags={"Package"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"tracking_number", "weight", "status"},
     *             @OA\Property(property="tracking_number", type="string"),
     *             @OA\Property(property="weight", type="number"),
     *             @OA\Property(property="status", type="string", enum={"pending", "in_transit", "delivered"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Paket berhasil ditambahkan",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", ref="#/components/schemas/Package")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'tracking_number' => 'required|unique:packages',
            'weight' => 'required|numeric',
            'status' => 'required|string|in:pending,in_transit,delivered',
        ]);

        $package = Package::create([
            'tracking_number' => $request->tracking_number,
            'weight' => $request->weight,
            'status' => $request->status,
        ]);

        return response()->json(['message' => 'Package created successfully', 'data' => $package], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/packages/{id}",
     *     summary="Memperbarui status paket",
     *     tags={"Package"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"status"},
     *             @OA\Property(property="status", type="string", enum={"pending", "in_transit", "delivered"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Paket berhasil diperbarui",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", ref="#/components/schemas/Package")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Paket tidak ditemukan",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $package = Package::findOrFail($id);

        $request->validate([
            'status' => 'sometimes|string|in:pending,in_transit,delivered',
        ]);

        if ($request->has('status')) {
            $package->update(['status' => $request->status]);

            TrackingUpdate::create([
                'package_id' => $package->id,
                'status' => $request->status,
                'description' => 'Status paket diperbarui',
            ]);
        }

        return response()->json(['message' => 'Package updated successfully', 'data' => $package]);
    }

    /**
     * @OA\Delete(
     *     path="/api/packages/{id}",
     *     summary="Menghapus paket",
     *     tags={"Package"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Paket berhasil dihapus",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Paket tidak ditemukan",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $package = Package::findOrFail($id);
        $package->delete();
        return response()->json(['message' => 'Package deleted successfully']);
    }

    /**
     * @OA\Get(
     *     path="/api/packages/{tracking_number}/track",
     *     summary="Melacak paket berdasarkan nomor pelacakan",
     *     tags={"Package"},
     *     @OA\Parameter(
     *         name="tracking_number",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Paket ditemukan dan data pelacakan berhasil ditampilkan",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/Package")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Paket tidak ditemukan",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
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
