<?php

namespace App\Http\Controllers\Publish;

use Illuminate\Http\Request;
use Automattic\WooCommerce\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\MasterData\Battery\BatteryModel;
use App\Models\MasterData\Battery\BatterySizeCategoryModel;



class DataBattery extends Controller
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
            'https://wp.raden.social/',
            'ck_e69e713763c055f1a63f9057dfae0bb595775815',
            'cs_1e35df9199bb4cd6e9e1774bf11e0831f089763e',
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

        // check if session has product data already
        if (session()->has('productData')) {
            $productData = session('productData');
        } else {
            $productData = $this->getProductAll();
            session(['productData' => $productData]);
        }

        $data = array(
            'products' => $productData,
        );

        return view(
            'Publish.DataBattery.index',
            getIndexData(
                $this->title,
                $data
            )
        );
    }

    /**
     * Synchronizes product data.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function syncProduct()
    {
        try {
            $productData = $this->getProductAll();
            session(['productData' => $productData]);

            return response()->json([
                'status' => 'success',
                'message' => 'Sync product data success',
            ]);
        } catch (\Throwable $th) {
            // logs the error message
            Log::error($th);
            return response()->json([
                'status' => 'error',
                'message' => 'Sync data to WooCommerce Category failed ' . $th->getMessage(),
            ]);
        }
    }

    public function viewDetails(Request $request)
    {
        try {
            $product = $this->getProductById($request->id);
            return response()->json([
                'status' => 'success',
                'message' => 'Product data retrieved successfully',
                'data' => $product
            ]);
        } catch (\Throwable $th) {
            Log::error($th);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get sales data' . $th->getMessage(),
                'data' => null
            ]);
        }
    }

    /**
     * Syncs data from the database to WooCommerce.
     *
     * This method synchronizes categories and products from the database to WooCommerce.
     * It checks if a category or product exists in WooCommerce and performs the following actions:
     * - If a category or product exists in the database but not in WooCommerce, it creates it in WooCommerce.
     * - If a category or product exists in both the database and WooCommerce but with different details, it updates it in WooCommerce.
     * - If a category or product exists in WooCommerce but not in the database, it deletes it from WooCommerce.
     *
     * @return \Illuminate\Http\JsonResponse The JSON response indicating the status of the synchronization process.
     * - If the synchronization is successful, the response will have a 'status' of 'success' and a 'message' indicating the success.
     * - If the synchronization fails, the response will have a 'status' of 'error' and a 'message' indicating the failure.
     */
    public function syncWooCommerce()
    {
        try {
            DB::beginTransaction();
            // get all categories from database 
            $categories = BatterySizeCategoryModel::all();
            $categoriesWoo = collect($this->getCategoryAll());

            // sync categories from database to woocommerce if not exist in woocommerce but exist in database then create it
            // if exist in woocommerce but not in database then delete it from woocommerce
            // if exist in both but different then update it in woocommerce
            foreach ($categories as $category) {
                if ($category->name == 'Uncategorized') continue;

                $categoryWoo = $this->findCategoryByName($categoriesWoo, $category->name);

                if ($categoryWoo == null) {
                    try {
                        $this->woocommerce->post('products/categories', [
                            'name' => $category->name,
                        ]);
                    } catch (\Exception $e) {
                        if (strpos($e->getMessage(), 'term_exists') !== false) {
                            // Handle the case where the category already exists
                            Log::warning("Category '{$category->name}' already exists in WooCommerce.");
                        } else {
                            throw $e;
                        }
                    }
                } else {
                    $this->woocommerce->put('products/categories/' . $categoriesWoo->id, [
                        'name' => $category->name,
                    ]);
                }
            }

            foreach ($categoriesWoo as $categoryWoo) {
                if ($categoryWoo->name == 'Uncategorized') continue;
                $category = $categories->firstWhere('name', $categoryWoo->name);
                if ($category == null) {
                    $this->woocommerce->delete('products/categories/' . $categoryWoo->id, [
                        'force' => true,
                    ]);
                }
            }

            // get all products from database
            $products = BatteryModel::all();
            $productsWoo = collect($this->getProductAll());

            // sync products from database to woocommerce if not exist in woocommerce but exist in database then create it
            // if exist in woocommerce but not in database then delete it from woocommerce
            // if exist in both but different then update it in woocommerce
            foreach ($products as $product) {
                $productWoo = $productsWoo->firstWhere('name', $product->name);
                $category = $categories->firstWhere('id', $product->category_id);
                $categoryWoo = $categoriesWoo->firstWhere('name', $category->name);
                $data = [
                    'name' => $product->name,
                    'type' => 'simple',
                    'regular_price' => $product->price,
                    'description' => $product->description,
                    'categories' => [
                        [
                            'id' => $categoryWoo->id,
                        ],
                    ],
                ];
                if ($productWoo == null) {
                    $this->woocommerce->post('products', $data);
                } else {
                    $this->woocommerce->put('products/' . $productWoo->id, $data);
                }
            }

            foreach ($productsWoo as $productWoo) {
                $product = $products->firstWhere('name', $productWoo->name);
                if ($product == null) {
                    $this->woocommerce->delete('products/' . $productWoo->id, [
                        'force' => true,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Sync data to WooCommerce success',
            ]);
        } catch (\Throwable $th) {

            DB::rollBack();
            // logs the error message
            Log::error($th);
            return response()->json([
                'status' => 'error',
                'message' => 'Sync data to WooCommerce failed ' . $th->getMessage(),
            ]);
        }
    }

    /**
     * Retrieves all products from the WooCommerce API.
     *
     * @return array The array of products retrieved from the WooCommerce API.
     */
    private function getProductAll()
    {
        return $this->woocommerce->get('products');
    }

    /**
     * Retrieves all categories from the WooCommerce API.
     *
     * @return array The list of categories.
     */
    private function getCategoryAll()
    {
        return $this->woocommerce->get('products/categories');
    }

    /**
     * Find a WooCommerce category by name in the WooCommerce categories array.
     *
     * @param \Illuminate\Support\Collection $categoriesWoo
     * @param string $name
     * @return array|null
     */
    private function findCategoryByName($categoriesWoo, $name)
    {
        foreach ($categoriesWoo as $categoryWoo) {
            if (isset($categoryWoo->name) && $categoryWoo->name == $name) {
                return $categoryWoo;
            }
        }

        return null;
    }

    private function getProductById($id)
    {
        return $this->woocommerce->get('products/' . $id);
    }
}
