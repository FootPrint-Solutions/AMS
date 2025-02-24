<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MasterData\Battery\BatteryModel;
use App\Models\MasterData\Battery\BatterySubbrandCategoryModel;

class Battery extends Controller
{
    /**
     * Get a random selection of batteries.
     *
     * This method retrieves a random selection of 12 batteries from the database.
     * If the operation is successful, it returns a JSON response with the status 'success',
     * a message indicating that data was found, and the data itself.
     * If an exception occurs, it returns a JSON response with the status 'error',
     * a message indicating that data was not found, and an empty data array.
     *
     * @param \Illuminate\Http\Request $request The incoming request instance.
     * @return \Illuminate\Http\JsonResponse The JSON response containing the status, message, and data.
     */
    function getRandomBattery(Request $request)
    {
        try {
            $battery = BatteryModel::inRandomOrder()->limit(12)->get()->toArray();
            $response = [
                'status' => 'success',
                'message' => 'Data found',
                'data' => $battery
            ];

            return response()->json($response);
        } catch (\Exception $e) {
            $response = [
                'status' => 'error',
                'message' => 'Data not found',
                'data' => []
            ];

            return response()->json($response, 500);
        }
    }

    /**
     * Retrieve battery categories with status 1.
     *
     * This method fetches all battery subbrand categories that have a status of 1
     * from the database and returns them in a JSON response.
     *
     * @param \Illuminate\Http\Request $request The incoming request instance.
     * 
     * @return \Illuminate\Http\JsonResponse A JSON response containing the status,
     *                                       message, and data (battery categories).
     */
    function getBatteryCategory(Request $request)
    {
        try {
            $battery = BatterySubbrandCategoryModel::get()->toArray();
            $response = [
                'status' => 'success',
                'message' => 'Data found',
                'data' => $battery
            ];

            return response()->json($response);
        } catch (\Exception $e) {
            $response = [
                'status' => 'error',
                'message' => 'Data not found',
                'data' => []
            ];

            return response()->json($response, 500);
        }
    }

    /**
     * Find batteries by category.
     *
     * This function retrieves batteries from the database based on the specified category.
     * It returns a JSON response with the status, message, and data.
     *
     * @param string $category The category of the batteries to find.
     * @return \Illuminate\Http\JsonResponse The JSON response containing the status, message, and data.
     */
    function findBatteriesByCategory($category)
    {
        try {
            $query = BatteryModel::query();

            if ($category != "all") {
                $query->where('subbrand_category_id', $category);
            } else {
                $query->inRandomOrder()->limit(12);
            }

            $battery = $query->get()->toArray();
            $response = [
                'status' => 'success',
                'message' => 'Data found',
                'data' => $battery
            ];

            return response()->json($response);
        } catch (\Exception $e) {
            $response = [
                'status' => 'error',
                'message' => 'Data not found',
                'data' => []
            ];

            return response()->json($response, 500);
        }
    }
}
