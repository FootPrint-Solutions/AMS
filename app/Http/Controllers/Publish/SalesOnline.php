<?php

namespace App\Http\Controllers\Publish;

use Illuminate\Http\Request;
use Automattic\WooCommerce\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;


class SalesOnline extends Controller
{
    private $title = 'Sales Online';
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
        // check if session has sales data already
        if (session()->has('salesData')) {
            $salesData = session('salesData');
        } else {
            $salesData = $this->getSalesAll();
            session(['salesData' => $salesData]);
        }

        $data = array(
            'Sales' => $salesData,
        );

        return view(
            'Publish.SalesOnline.index',
            getIndexData(
                $this->title,
                $data
            )
        );
    }

    /**
     * Retrieves the sales data by the given sales online number.
     *
     * @param \Illuminate\Http\Request $request The HTTP request object.
     *
     * @return \Illuminate\Http\JsonResponse The JSON response containing the sales data.
     */
    public function viewDetails(Request $request)
    {
        try {
            $SalesData = $this->getSalesById($request->sales_online_number);
            return response()->json([
                'status' => 'success',
                'message' => 'Sales data retrieved successfully',
                'data' => $SalesData
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

    public function syncSalesOnline()
    {
        try {
            $salesData = $this->getSalesAll();
            session(['salesData' => $salesData]);

            return response()->json([
                'status' => 'success',
                'message' => 'Sync data to WooCommerce Sales Online successful',
            ]);
        } catch (\Throwable $th) {
            Log::error($th);
            return response()->json([
                'status' => 'error',
                'message' => 'Sync data to WooCommerce Sales Online failed ' . $th->getMessage(),
            ]);
        }
    }

    /**
     * Retrieves all sales from the WooCommerce API.
     *
     * @return array The sales data.
     */
    private function getSalesAll()
    {
        $sales = $this->woocommerce->get('orders');
        return $sales;
    }

    /**
     * Retrieves a specific sale from the WooCommerce API.
     *
     * @param int $id The ID of the sale.
     * @return array The sale data.
     */
    private function getSalesById($id)
    {
        $sales = $this->woocommerce->get('orders/' . $id);
        return $sales;
    }
}
