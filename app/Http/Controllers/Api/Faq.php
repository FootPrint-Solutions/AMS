<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Publish\FaqModel;
use Illuminate\Support\Facades\DB;

class Faq extends Controller
{
    public function getAllFaq(Request $request)
    {
        try {
            $faq = FaqModel::where('status', 1)->get();
            return response()->json([
                'status' => "success",
                'message' => 'Success',
                'data' => $faq
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
