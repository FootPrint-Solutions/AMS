<?php

namespace App\Http\Controllers\Publish;

use Illuminate\Http\Request;
use Automattic\WooCommerce\Client;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\MasterData\Vehicle\VehicleBrandModel;
use App\Models\MasterData\Vehicle\VehicleModel;

class DataCategory extends Controller
{
    private $title = 'Data Battery';
    private $woocommerce;

    /**
     * Initializes the WooCommerce Client in the constructor.
     *
     * @return void
     */
    public function __construct()
    {
        // Initialize WooCommerce Client in the constructor
        $this->woocommerce = new Client(
            'https://akikita.web.id/',
            'ck_7034e6f1e7a7d3b705df60c37bb003c6a1ca6f9b',
            'cs_b7973fc68cdd299d2ad6647989872e517d88cab0',
            [
                'version' => 'wc/v3',
            ]
        );
    }

    /**
     * Display the index page for the DataBattery controller.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        try {
            // check if session has product data already
            if (session()->has('categoryData')) {
                $categoryData = session('categoryData');
            } else {
                $categoryData = $this->getCategoryAll();
                session(['categoryData' => $categoryData]);
            }

            $data = array(
                'category' => $categoryData,
            );

            return view(
                'Publish.DataCategory.index',
                getIndexData(
                    $this->title,
                    $data
                )
            );
        } catch (\Throwable $th) {
            Log::error($th);
            return redirect()->route('dashboard')->with('error', 'Failed to get data battery category');
        }
    }

    /**
     * Synchronizes category data.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function syncCategory()
    {
        try {
            $categoryData = $this->getCategoryAll();
            session(['categoryData' => $categoryData]);

            return response()->json([
                'status' => 'success',
                'message' => 'Sync category data success',
                'data' => $categoryData,
            ]);
        } catch (\Exception $e) {
            // logs the error message
            Log::error($e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Sync data to WooCommerce Category failed ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Count the number of parent categories.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function countParentCategory()
    {
        try {
            $vehicleBrand = VehicleBrandModel::all();

            return response()->json([
                'status' => 'success',
                'message' => 'Success to get vehicle brand data',
                'data' => count($vehicleBrand),
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get vehicle brand data' . $th->getMessage(),
            ]);
        }
    }

    /**
     * Sends the parent category partially.
     *
     * @param \Illuminate\Http\Request $request The request object.
     *
     * @return \Illuminate\Http\JsonResponse The JSON response containing the status, message, and data.
     */
    public function sendParentCategoryPartially(Request $request)
    {
        try {
            $limit = $request->limit;
            $offset = $request->offset;

            $vehicleBrand = VehicleBrandModel::offset($offset)->limit($limit)->get();
            $dataParentStatus = [];

            foreach ($vehicleBrand as $brand) {
                $brandName = $brand->name ?? 'Uncategorized';
                $brandSlug = strtolower(str_replace(' ', '-', $brandName));
                $findBrandSlug = $this->woocommerce->get('products/categories', ['slug' => $brandSlug]);

                if ($findBrandSlug == null || empty($findBrandSlug)) {
                    $data = [
                        'name' => $brandName,
                        'slug' => $brandSlug,
                        'description' => 'Parent Category Brand ' . $brandName,
                        'parent' => 0,
                    ];

                    $response = $this->woocommerce->post('products/categories', $data);

                    $dataParentStatus[] = [
                        'status' => 'success',
                        'message' => 'Success to send ' . $brandName . ' data Parent Category',
                    ];
                } else {
                    $this->woocommerce->put('products/categories/' . $findBrandSlug[0]->id, [
                        'name' => $brandName
                    ]);

                    $dataParentStatus[] = [
                        'status' => 'success',
                        'message' => 'Success to update ' . $brandName . ' data Parent Category',
                    ];
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Success to send vehicle brand data',
                'data' => $dataParentStatus,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send vehicle brand data' . $th->getMessage(),
            ]);
        }
    }

    public function countCategory()
    {
        try {
            $categoryData = VehicleModel::all();

            return response()->json([
                'status' => 'success',
                'message' => 'Success to get category data',
                'data' => count($categoryData),
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get category data' . $th->getMessage(),
            ]);
        }
    }

    public function sendCategoryPartially(Request $request)
    {
        try {
            $limit = $request->limit;
            $offset = $request->offset;

            $category = VehicleModel::with('brand')->offset($offset)->limit($limit)->get();
            $dataCategoryStatus = [];

            foreach ($category as $item) {
                $brandName = $item->brand->name ?? 'Uncategorized';
                $brandSlug = strtolower(str_replace(' ', '-', $brandName));
                $findBrandSlug = $this->woocommerce->get('products/categories', ['slug' => $brandSlug]);

                if ($findBrandSlug == null || empty($findBrandSlug)) {
                    $data = [
                        'name' => $brandName,
                        'slug' => $brandSlug,
                        'description' => 'Parent Category Brand ' . $brandName,
                        'parent' => 0,
                    ];

                    $response = $this->woocommerce->post('products/categories', $data);

                    $dataCategoryStatus[] = [
                        'status' => 'success',
                        'message' => 'Success to send ' . $brandName . ' data Parent Category',
                    ];
                } else {
                    $this->woocommerce->put('products/categories/' . $findBrandSlug[0]->id, [
                        'name' => $brandName
                    ]);

                    $dataCategoryStatus[] = [
                        'status' => 'success',
                        'message' => 'Success to update ' . $brandName . ' data Parent Category',
                    ];
                }

                $categoryName = $item->name ?? 'Uncategorized';
                $categorySlug = strtolower(str_replace(' ', '-', $categoryName));
                $findCategorySlug = $this->woocommerce->get('products/categories', ['slug' => $categorySlug]);

                if ($findCategorySlug == null || empty($findCategorySlug)) {
                    $data = [
                        'name' => $categoryName,
                        'slug' => $categorySlug,
                        'description' => 'Category ' . $categoryName,
                        'parent' => $findBrandSlug[0]->id,
                    ];

                    $response = $this->woocommerce->post('products/categories', $data);

                    $dataCategoryStatus[] = [
                        'status' => 'success',
                        'message' => 'Success to send ' . $categoryName . ' data Category with parent ' . $brandName,
                    ];
                } else {
                    $this->woocommerce->put('products/categories/' . $findCategorySlug[0]->id, [
                        'name' => $categoryName
                    ]);

                    $dataCategoryStatus[] = [
                        'status' => 'success',
                        'message' => 'Success to update ' . $categoryName . ' data Category',
                    ];
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Success to send category data',
                'data' => $dataCategoryStatus,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send category data' . $th->getMessage(),
            ]);
        }
    }

    /**
     * Get all categories from WooCommerce.
     *
     * @return array
     */
    private function getCategoryAll()
    {
        $categoryData = $this->woocommerce->get('products/categories');
        return $categoryData;
    }
}
