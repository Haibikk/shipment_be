<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KategoriBarang;

/**
 * @OA\Tag(
 *     name="KategoriBarang",
 *     description="API untuk mengelola kategori barang"
 * )
 */

/**
 * @OA\Schema(
 *     schema="KategoriBarang",
 *     type="object",
 *     required={"nama_kategori"},
 *     @OA\Property(property="id_kategori", type="integer", description="ID Kategori Barang"),
 *     @OA\Property(property="nama_kategori", type="string", description="Nama Kategori Barang"),
 *     @OA\Property(property="deskripsi", type="string", description="Deskripsi Kategori Barang"),
 *     @OA\Property(property="tarif_asuransi", type="number", format="float", description="Tarif Asuransi untuk Kategori Barang"),
 *     @OA\Property(property="penanganan_khusus", type="string", description="Instruksi Penanganan Khusus"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Waktu Pembuatan"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Waktu Terakhir Diperbarui")
 * )
 */

class KategoriBarangController extends Controller
{
    /**
     * @OA\Get(
     *     path="/kategori-barang",
     *     summary="Mengambil semua kategori barang",
     *     tags={"KategoriBarang"},
     *     @OA\Response(
     *         response=200,
     *         description="Daftar kategori barang berhasil diambil",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/KategoriBarang"))
     *         )
     *     )
     * )
     */
    public function index()
    {
        $kategoriBarang = KategoriBarang::all();
        return response()->json(['data' => $kategoriBarang]);
    }

    /**
     * @OA\Post(
     *     path="/kategori-barang",
     *     summary="Menambahkan kategori barang baru",
     *     tags={"KategoriBarang"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nama_kategori"},
     *             @OA\Property(property="nama_kategori", type="string"),
     *             @OA\Property(property="deskripsi", type="string"),
     *             @OA\Property(property="tarif_asuransi", type="number", format="float"),
     *             @OA\Property(property="penanganan_khusus", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Kategori barang berhasil ditambahkan",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", ref="#/components/schemas/KategoriBarang")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validasi gagal",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tarif_asuransi' => 'nullable|numeric|min:0',
            'penanganan_khusus' => 'nullable|string'
        ]);

        $kategoriBarang = KategoriBarang::create($request->all());
        return response()->json(['message' => 'Kategori barang berhasil ditambahkan', 'data' => $kategoriBarang], 201);
    }

    /**
     * @OA\Get(
     *     path="/kategori-barang/{id}",
     *     summary="Mengambil detail kategori barang berdasarkan ID",
     *     tags={"KategoriBarang"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detail kategori barang berhasil diambil",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/KategoriBarang")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Kategori barang tidak ditemukan",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $kategoriBarang = KategoriBarang::findOrFail($id);
        return response()->json(['data' => $kategoriBarang]);
    }

    /**
     * @OA\Put(
     *     path="/kategori-barang/{id}",
     *     summary="Memperbarui kategori barang",
     *     tags={"KategoriBarang"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="nama_kategori", type="string"),
     *             @OA\Property(property="deskripsi", type="string"),
     *             @OA\Property(property="tarif_asuransi", type="number", format="float"),
     *             @OA\Property(property="penanganan_khusus", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Kategori barang berhasil diperbarui",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", ref="#/components/schemas/KategoriBarang")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Kategori barang tidak ditemukan",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validasi gagal",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $kategoriBarang = KategoriBarang::findOrFail($id);
        
        $request->validate([
            'nama_kategori' => 'sometimes|required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tarif_asuransi' => 'nullable|numeric|min:0',
            'penanganan_khusus' => 'nullable|string'
        ]);

        $kategoriBarang->update($request->all());
        return response()->json(['message' => 'Kategori barang berhasil diperbarui', 'data' => $kategoriBarang]);
    }

    /**
     * @OA\Delete(
     *     path="/kategori-barang/{id}",
     *     summary="Menghapus kategori barang",
     *     tags={"KategoriBarang"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Kategori barang berhasil dihapus",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Kategori barang tidak ditemukan",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $kategoriBarang = KategoriBarang::findOrFail($id);
        $kategoriBarang->delete();
        return response()->json(['message' => 'Kategori barang berhasil dihapus']);
    }
} 