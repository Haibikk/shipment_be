namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeliveryZone;

/**
 * @OA\Tag(
 *     name="DeliveryZone",
 *     description="API untuk mengelola zona pengiriman"
 * )
 */
class DeliveryZoneController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/delivery-zones",
     *     summary="Mengambil semua zona pengiriman",
     *     tags={"DeliveryZone"},
     *     @OA\Response(
     *         response=200,
     *         description="Daftar zona pengiriman berhasil diambil",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/DeliveryZone"))
     *         )
     *     )
     * )
     */
    public function index()
    {
        $zones = DeliveryZone::all();
        return response()->json(['data' => $zones]);
    }

    /**
     * @OA\Post(
     *     path="/api/delivery-zones",
     *     summary="Menambahkan zona pengiriman baru",
     *     tags={"DeliveryZone"},
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
     *         description="Zona pengiriman berhasil ditambahkan",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", ref="#/components/schemas/DeliveryZone")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:delivery_zones',
            'description' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
        ]);

        $zone = DeliveryZone::create($request->all());

        return response()->json(['message' => 'Delivery zone created successfully', 'data' => $zone], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/delivery-zones/{id}",
     *     summary="Mengambil detail zona pengiriman berdasarkan ID",
     *     tags={"DeliveryZone"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detail zona pengiriman berhasil diambil",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/DeliveryZone")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Zona pengiriman tidak ditemukan",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $zone = DeliveryZone::findOrFail($id);
        return response()->json(['data' => $zone]);
    }

    /**
     * @OA\Put(
     *     path="/api/delivery-zones/{id}",
     *     summary="Memperbarui informasi zona pengiriman",
     *     tags={"DeliveryZone"},
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
     *         description="Zona pengiriman berhasil diperbarui",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", ref="#/components/schemas/DeliveryZone")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Zona pengiriman tidak ditemukan",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $zone = DeliveryZone::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'code' => 'sometimes|string|max:10|unique:delivery_zones,code,' . $id,
            'description' => 'nullable|string',
            'base_price' => 'sometimes|numeric|min:0',
        ]);

        $zone->update($request->all());

        return response()->json(['message' => 'Delivery zone updated successfully', 'data' => $zone]);
    }

    /**
     * @OA\Delete(
     *     path="/api/delivery-zones/{id}",
     *     summary="Menghapus zona pengiriman",
     *     tags={"DeliveryZone"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Zona pengiriman berhasil dihapus",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Zona pengiriman tidak ditemukan",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $zone = DeliveryZone::findOrFail($id);
        $zone->delete();
        return response()->json(['message' => 'Delivery zone deleted successfully']);
    }
}
