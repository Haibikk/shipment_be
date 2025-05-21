<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BarangController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *     path="/api/barang",
     *     summary="Get list of barang",
     *     tags={"Barang"},
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function index()
    {
        return response()->json(Barang::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @OA\Post(
     *     path="/api/barang",
     *     summary="Create a new barang",
     *     tags={"Barang"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"id_kategori","nama_barang","berat","dimensi"},
     *             @OA\Property(property="id_kategori", type="integer"),
     *             @OA\Property(property="nama_barang", type="string"),
     *             @OA\Property(property="deskripsi", type="string"),
     *             @OA\Property(property="berat", type="number"),
     *             @OA\Property(property="dimensi", type="string"),
     *             @OA\Property(property="status", type="string")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Created")
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_kategori' => 'required|exists:kategori_barang,id_kategori',
            'nama_barang' => 'required|string',
            'deskripsi' => 'nullable|string',
            'berat' => 'required|numeric',
            'dimensi' => 'required|string',
            'status' => 'nullable|string',
        ]);
        $barang = Barang::create($validated);
        return response()->json($barang, 201);
    }

    /**
     * Display the specified resource.
     *
     * @OA\Get(
     *     path="/api/barang/{id}",
     *     summary="Get a barang by ID",
     *     tags={"Barang"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show($id)
    {
        $barang = Barang::find($id);
        if (!$barang) {
            return response()->json(['message' => 'Not Found'], 404);
        }
        return response()->json($barang);
    }

    /**
     * Update the specified resource in storage.
     *
     * @OA\Put(
     *     path="/api/barang/{id}",
     *     summary="Update a barang",
     *     tags={"Barang"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="id_kategori", type="integer"),
     *             @OA\Property(property="nama_barang", type="string"),
     *             @OA\Property(property="deskripsi", type="string"),
     *             @OA\Property(property="berat", type="number"),
     *             @OA\Property(property="dimensi", type="string"),
     *             @OA\Property(property="status", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function update(Request $request, $id)
    {
        $barang = Barang::find($id);
        if (!$barang) {
            return response()->json(['message' => 'Not Found'], 404);
        }
        $validated = $request->validate([
            'id_kategori' => 'sometimes|exists:kategori_barang,id_kategori',
            'nama_barang' => 'sometimes|string',
            'deskripsi' => 'nullable|string',
            'berat' => 'sometimes|numeric',
            'dimensi' => 'sometimes|string',
            'status' => 'nullable|string',
        ]);
        $barang->update($validated);
        return response()->json($barang);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @OA\Delete(
     *     path="/api/barang/{id}",
     *     summary="Delete a barang",
     *     tags={"Barang"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=204, description="No Content"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function destroy($id)
    {
        $barang = Barang::find($id);
        if (!$barang) {
            return response()->json(['message' => 'Not Found'], 404);
        }
        $barang->delete();
        return response()->json(null, 204);
    }
} 