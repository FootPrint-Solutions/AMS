<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Publish\GalleryModel;
use Illuminate\Support\Facades\DB;

class Gallery extends Controller
{
    public function getAllGallery(Request $request)
    {
        try {
            $gallery = GalleryModel::where('status', 1)->with(['battery', 'vehicle'])->get();
            return response()->json([
                'status' => "success",
                'message' => 'Success',
                'data' => $gallery
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => "error",
                'message' => 'Failed',
                'data' => null
            ], 500);
        }
    }
}
