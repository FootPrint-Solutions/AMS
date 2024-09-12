<?php

namespace App\Http\Controllers\Publish;

use Illuminate\Http\Request;
use Automattic\WooCommerce\Client;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

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
