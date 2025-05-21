<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DeliveryZone;

/**
 * @OA\Tag(
 *     name="AreaPengiriman",
 *     description="API untuk mengelola area pengiriman"
 * )
 */

/**
 * @OA\Schema(
 *     schema="AreaPengiriman",
 *     type="object",
 *     required={"name", "code", "base_price"},
 *     @OA\Property(property="id", type="integer", description="ID Area Pengiriman"),
 *     @OA\Property(property="name", type="string", description="Nama Area Pengiriman"),
 *     @OA\Property(property="code", type="string", description="Kode Area Pengiriman"),
 *     @OA\Property(property="description", type="string", description="Deskripsi Area Pengiriman"),
 *     @OA\Property(property="base_price", type="number", format="float", description="Harga Dasar Area Pengiriman")
 * )
 */

class DeliveryZoneController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/area-pengiriman",
     *     summary="Mengambil semua area pengiriman",
     *     tags={"AreaPengiriman"},
     *     @OA\Response(
     *         response=200,
     *         description="Daftar area pengiriman berhasil diambil",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/AreaPengiriman"))
     *         )
     *     )
     * )
     */
    public function index()
    {
        return response()->json(DeliveryZone::all());
    }

    /**
     * @OA\Post(
     *     path="/api/area-pengiriman",
     *     summary="Menambahkan area pengiriman baru",
     *     tags={"AreaPengiriman"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "code", "base_price"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="code", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="base_price", type="number", format="float")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Area pengiriman berhasil ditambahkan",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", ref="#/components/schemas/AreaPengiriman")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:area_pengiriman',
            'description' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
        ]);
        $deliveryZone = DeliveryZone::create($validated);
        return response()->json($deliveryZone, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/area-pengiriman/{id}",
     *     summary="Mengambil detail area pengiriman berdasarkan ID",
     *     tags={"AreaPengiriman"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detail area pengiriman berhasil diambil",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/AreaPengiriman")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Area pengiriman tidak ditemukan",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $deliveryZone = DeliveryZone::find($id);
        if (!$deliveryZone) {
            return response()->json(['message' => 'Not Found'], 404);
        }
        return response()->json($deliveryZone);
    }

    /**
     * @OA\Put(
     *     path="/api/area-pengiriman/{id}",
     *     summary="Memperbarui informasi area pengiriman",
     *     tags={"AreaPengiriman"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="code", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="base_price", type="number", format="float")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Area pengiriman berhasil diperbarui",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", ref="#/components/schemas/AreaPengiriman")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Area pengiriman tidak ditemukan",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $deliveryZone = DeliveryZone::find($id);
        if (!$deliveryZone) {
            return response()->json(['message' => 'Not Found'], 404);
        }
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'code' => 'sometimes|string|max:10|unique:area_pengiriman,code,' . $id,
            'description' => 'nullable|string',
            'base_price' => 'sometimes|numeric|min:0',
        ]);
        $deliveryZone->update($validated);
        return response()->json($deliveryZone);
    }

    /**
     * @OA\Delete(
     *     path="/api/area-pengiriman/{id}",
     *     summary="Menghapus area pengiriman",
     *     tags={"AreaPengiriman"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Area pengiriman berhasil dihapus",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Area pengiriman tidak ditemukan",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $deliveryZone = DeliveryZone::find($id);
        if (!$deliveryZone) {
            return response()->json(['message' => 'Not Found'], 404);
        }
        $deliveryZone->delete();
        return response()->json(null, 204);
    }
}
