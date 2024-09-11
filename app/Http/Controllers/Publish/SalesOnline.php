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
        $data = array(
            'Sales' => $this->getSalesAll(),
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
     * Retrieves all sales from the WooCommerce API.
     *
     * @return array The sales data.
     */
    private function getSalesAll()
    {
        $sales = $this->woocommerce->get('orders');
        return $sales;
    }
}
