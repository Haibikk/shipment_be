<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * @OA\Get(
     *   path="/category",
     *   tags={"Category"},
     *   operationId="listCategory",
     *   summary="List of Categories",
     *   description="Retrieve a list of book categories",
     *   @OA\Response(
     *     response=200,
     *     description="Successful operation",
     *     @OA\JsonContent(
     *       example={
     *         "success": true,
     *         "message": "Successfully retrieved categories",
     *         "data": {
     *           {"id": 1, "name": "Fiction"},
     *           {"id": 2, "name": "Non-Fiction"}
     *         }
     *       }
     *     )
     *   )
     * )
     */
    public function listCategory()
    {
        return response()->json([
            'success' => true,
            'message' => 'Successfully retrieved categories',
            'data' => [
                ['id' => 1, 'name' => 'Fiction'],
                ['id' => 2, 'name' => 'Non-Fiction'],
            ],
        ]);
    }
}
