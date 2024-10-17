<?php

namespace App\Http\Controllers\Publish;

use Illuminate\Http\Request;
use Automattic\WooCommerce\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\MasterData\Battery\BatteryModel;
use App\Models\MasterData\Battery\BatterySizeCategoryModel;
use App\Models\MasterData\Vehicle\VehicleModel;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

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

    public function countProduct()
    {
        try {
            $product = BatteryModel::all();
            return response()->json([
                'status' => 'success',
                'message' => 'Success to get product data',
                'data' => count($product),
            ]);
        } catch (\Throwable $th) {
            Log::error($th);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get product data' . $th->getMessage(),
            ]);
        }
    }

    public function sendProductPartially(Request $request)
    {
        try {
            $limit = $request->limit;
            $offset = $request->offset;

            $product = BatteryModel::limit($limit)->offset($offset)->with('VehicleBattery', 'batteryPricesBelong')->get();
            $dataProductStatus = [];

            foreach ($product as $key) {
                $categori = $key->VehicleBattery->vehicle->name ?? 'Uncategorized';
                $categorySlug = strtolower(str_replace(' ', '-', $categori));
                $category = $this->findCategoryWooBySlug($categorySlug);
                $price = (string) $key->batteryPricesBelong->price_retail;

                // jika category tidak ditemukan maka buat category baru
                if ($category == null || count($category) == 0) {
                    $this->woocommerce->post('products/categories', [
                        'name' => $categori,
                        'slug' => $categorySlug,
                    ]);

                    $category = $this->findCategoryWooBySlug($categorySlug);
                }

                $data = [
                    'name' => $key->name,
                    'type' => 'simple',
                    'regular_price' => (string) $price,
                    'description' => $key->description,
                    'categories' => [
                        [
                            'id' => $category[0]->id,
                        ],
                    ],
                ];

                $productWoo = $this->findProductWooByName($key->name);

                if ($productWoo == null || count($productWoo) == 0) {
                    // Produk tidak ditemukan, buat produk baru
                    $this->woocommerce->post('products', $data);

                    $dataProductStatus[] = [
                        'status' => 'success',
                        'message' => 'Success to send ' . $key->name . ' product with category ' . $categori,
                    ];

                    // set logs 
                    Log::info('Product created', ['product' => $key->name]);
                    Log::info('Response Create', ['response' => $response]);
                } else {
                    // Produk ditemukan, pastikan ID valid
                    if (!isset($productWoo[0]->id)) {
                        throw new \Exception('Invalid product ID for ' . $key->name);
                    }

                    // Lakukan update produk
                    $response =  $this->woocommerce->put('products/' . $productWoo[0]->id, $data);

                    // set logs 
                    Log::info('Product updated', ['product' => $key->name]);
                    Log::info('Response Update', ['response' => $response]);



                    $dataProductStatus[] = [
                        'status' => 'success',
                        'message' => 'Success to update ' . $key->name . ' product with category ' . $categori,
                    ];
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Success to send data product',
                'data' => $dataProductStatus,
            ]);
        } catch (\Throwable $th) {
            Log::error($th);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send product data' . $th->getMessage(),
            ]);
        }
    }

    /**
     * Retrieves the details of a product.
     *
     * @param \Illuminate\Http\Request $request The HTTP request object.
     *
     * @return \Illuminate\Http\JsonResponse The JSON response containing the product details.
     *
     * @throws \Throwable If an error occurs while retrieving the product details.
     */
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
     * Sends the product data to the WooCommerce API.
     *
     * @return \Illuminate\Http\JsonResponse The JSON response containing the status, message, and data.
     */
    public function sendProduct()
    {
        try {
            // set vehicle as category 
            $vehicle = VehicleModel::all();

            $dataCategoryStatus = [];
            foreach ($vehicle as $cat) {
                $category = $this->findCategoryWooByName($cat->name);
                if ($category == null || count($category) == 0) {
                    $slug = strtolower(str_replace(' ', '-', $cat->name));
                    $this->woocommerce->post('products/categories', [
                        'name' => $cat->name,
                        'slug' => $cat->name,
                    ]);

                    // set data category status
                    $dataCategoryStatus[] = [
                        'status' => 'success',
                        'message' => 'Success to send ' . $cat->name . ' category',
                    ];
                } else {
                    $this->woocommerce->put('products/categories/' . $category[0]->id, [
                        'name' => $cat->name,
                    ]);

                    // set data category status
                    $dataCategoryStatus[] = [
                        'status' => 'success',
                        'message' => 'Success to update ' . $cat->name . ' category',
                    ];
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Success to send ' . $cat->name . ' category',
                'data' => $dataCategoryStatus,
            ]);
        } catch (\Throwable $th) {
            Log::error($th);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send product data' . $th->getMessage(),
            ]);
        }
    }

    /**
     * Count the number of categories.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function countCategory()
    {
        try {
            $category = VehicleModel::all();
            return response()->json([
                'status' => 'success',
                'message' => 'Success to get category data',
                'data' => count($category),
            ]);
        } catch (\Throwable $th) {
            Log::error($th);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get category data' . $th->getMessage(),
            ]);
        }
    }

    /**
     * Sends category data partially.
     *
     * @param \Illuminate\Http\Request $request The request object.
     *
     * @return \Illuminate\Http\JsonResponse The JSON response containing the status, message, and data.
     */
    public function sendCategoryPartially(Request $request)
    {
        try {
            $limit = $request->limit;
            $offset = $request->offset;

            $category = VehicleModel::limit($limit)->offset($offset)->get();
            $dataCategoryStatus = [];
            foreach ($category as $cat) {
                $slug = strtolower(str_replace(' ', '-', $cat->name));
                $category = $this->findCategoryWooBySlug($slug);
                if ($category == null || count($category) == 0) {
                    $this->woocommerce->post('products/categories', [
                        'name' => $cat->name,
                        'slug' => $slug,
                    ]);

                    // set data category status
                    $dataCategoryStatus[] = [
                        'status' => 'success',
                        'message' => 'Success to send ' . $cat->name . ' category',
                    ];
                } else {
                    $this->woocommerce->put('products/categories/' . $category[0]->id, [
                        'name' => $cat->name,
                    ]);

                    // set data category status
                    $dataCategoryStatus[] = [
                        'status' => 'success',
                        'message' => 'Success to update ' . $cat->name . ' category',
                    ];
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Success to send data Category',
                'data' => $dataCategoryStatus,
            ]);
        } catch (\Throwable $th) {
            Log::error($th);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send product data' . $th->getMessage(),
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

    public function exportCsv()
    {
        // Get file excell 
        $file = public_path('template/excel/Detailed_Vehicle_Database.xlsx');

        // Read the file
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);

        // Get the active sheet
        $sheet = $spreadsheet->getActiveSheet();
        // get all data from excell start from row 2
        $data = $sheet->rangeToArray('A2:J' . $sheet->getHighestRow());

        // get battery name from battery size category
        foreach ($data as $key => $value) {
            $battery = BatterySizeCategoryModel::where('name', $value[6])->with('batteries')->first();
            $dataBatteryName = [];
            foreach ($battery->batteries as $bat) {
                $dataBatteryName[] = $bat->name;
            }

            $data[$key][10] = implode(', ', $dataBatteryName);
        }

        $battery = BatteryModel::with('vehicleBatteryBelong')->get();
        return view('Publish.DataBattery.exportCsv', compact('battery', 'data'));
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

    /**
     * Retrieves a product by its ID.
     *
     * @param int $id The ID of the product.
     * @return mixed The product data.
     */
    private function getProductById($id)
    {
        return $this->woocommerce->get('products/' . $id);
    }


    /**
     * Find a WooCommerce category by name in the WooCommerce categories array.
     *
     * @param string $name
     * @return array|null
     */
    private function findCategoryWooByName($name)
    {
        return $this->woocommerce->get('products/categories', ['name' => $name]);
    }

    /**
     * Find a WooCommerce product by name in the WooCommerce products array.
     *
     * @param string $name
     * @return array|null
     */
    private function findProductWooByName($name)
    {
        return $this->woocommerce->get('products', ['search' => $name]);
    }

    /**
     * Find a WooCommerce category by slug in the WooCommerce categories array.
     *
     * @param string $slug
     * @return array|null
     */
    private function findCategoryWooBySlug($slug)
    {
        $category = $this->woocommerce->get('products/categories', ['slug' => $slug]);
        Log::info('Category from WooCommerce:', ['category' => $category]);
        return $category;
    }
}
