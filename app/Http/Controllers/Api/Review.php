<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Publish\ReviewsModel;

class Review extends Controller
{
    public function getAllReview(Request $request)
    {
        try {
            $review = ReviewsModel::with(['vehicle'])->get();
            return response()->json([
                'status' => "success",
                'message' => 'Success',
                'data' => $review
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
