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
     *     path="/barang",
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
     *     path="/barang",
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
     *             @OA\Property(property="status", type="string", enum={"pending", "active", "inactive"})
     *         )
     *     ),
     *     @OA\Response(response=201, description="Created"),
     *     @OA\Response(response=422, description="Validation Error"),
     *     @OA\Response(response=500, description="Server Error")
     * )
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'id_kategori' => 'required|integer|exists:kategori_barang,id_kategori',
                'nama_barang' => 'required|string|max:255',
                'deskripsi' => 'nullable|string',
                'berat' => 'required|numeric|min:0',
                'dimensi' => 'required|string|max:50',
                'status' => 'nullable|string|in:pending,active,inactive',
            ]);

            $barang = Barang::create($validated);

            return response()->json([
                'message' => 'Barang created successfully',
                'data' => $barang
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create barang',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @OA\Get(
     *     path="/barang/{id}",
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
     *     path="/barang/{id}",
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
     *     path="/barang/{id}",
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