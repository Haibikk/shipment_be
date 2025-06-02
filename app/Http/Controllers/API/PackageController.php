<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Package;
use App\Models\TrackingUpdate;
use Illuminate\Support\Str;

/**
 * @OA\Tag(
 *     name="Paket",
 *     description="API untuk mengelola data paket"
 * )
 */

/**
 * @OA\Schema(
 *     schema="Paket",
 *     type="object",
 *     required={"name", "weight", "destination"},
 *     @OA\Property(property="id", type="integer", description="ID Paket"),
 *     @OA\Property(property="name", type="string", description="Nama Paket"),
 *     @OA\Property(property="weight", type="number", format="float", description="Berat Paket"),
 *     @OA\Property(property="destination", type="string", description="Tujuan Paket"),
 *     @OA\Property(property="status", type="string", description="Status Pengiriman Paket")
 * )
 */

class PackageController extends Controller
{
    /**
     * @OA\Get(
     *     path="/paket",
     *     summary="Mengambil semua data paket",
     *     tags={"Paket"},
     *     @OA\Response(
     *         response=200,
     *         description="Daftar paket berhasil diambil",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Paket"))
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
     *     path="/paket",
     *     security={{"bearerAuth":{}}},
     *     summary="Menambahkan data paket baru",
     *     tags={"Paket"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"tracking_number", "weight", "status", "sender_id", "receiver_id", "item_name", "origin_address", "destination_address"},
     *             @OA\Property(property="tracking_number", type="string"),
     *             @OA\Property(property="sender_id", type="integer"),
     *             @OA\Property(property="receiver_id", type="integer"),
     *             @OA\Property(property="item_name", type="string"),
     *             @OA\Property(property="weight", type="number"),
     *             @OA\Property(property="origin_address", type="string"),
     *             @OA\Property(property="destination_address", type="string"),
     *             @OA\Property(property="status", type="string", enum={"pending", "in_transit", "delivered"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Paket berhasil ditambahkan",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", ref="#/components/schemas/Paket")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'tracking_number' => 'required|unique:packages',
            'sender_id' => 'required|exists:users,id',
            'receiver_id' => 'required|exists:users,id',
            'item_name' => 'required|string',
            'weight' => 'required|numeric',
            'origin_address' => 'required|string',
            'destination_address' => 'required|string',
            'status' => 'required|string|in:pending,in_transit,delivered',
        ]);

        $package = Package::create([
            'tracking_number' => $request->tracking_number,
            'sender_id' => $request->sender_id,
            'receiver_id' => $request->receiver_id,
            'item_name' => $request->item_name,
            'weight' => $request->weight,
            'origin_address' => $request->origin_address,
            'destination_address' => $request->destination_address,
            'status' => $request->status,
        ]);

        return response()->json([
            'message' => 'Paket berhasil dibuat',
            'data' => $package
        ], 201);
    }

    /**
     * @OA\Put(
     *     path="/paket/{id}",
     * security={{"bearerAuth":{}}},
     *     summary="Memperbarui status sebuah paket",
     *     tags={"Paket"},
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
     *             @OA\Property(property="data", ref="#/components/schemas/Paket")
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

        return response()->json(['message' => 'Paket berhasil diperbarui', 'data' => $package]);
    }

    /**
     * @OA\Delete(
     *     path="/paket/{id}",
     * security={{"bearerAuth":{}}},
     *     summary="Menghapus data paket",
     *     tags={"Paket"},
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
        return response()->json(['message' => 'Paket berhasil dihapus']);
    }

    /**
     * @OA\Get(
     *     path="/paket/{tracking_number}/track",
     * security={{"bearerAuth":{}}},
     *     summary="Melacak paket berdasarkan nomor pelacakan",
     *     tags={"Paket"},
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
     *             @OA\Property(property="data", ref="#/components/schemas/Paket")
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
