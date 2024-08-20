<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Inventory extends Controller
{
    private $title = "Battery";
    private $service;

    // Constant sheet id.
    const SHEET_ID = "1uqSuVPyl181fZKCEvqEqrIp0hQz7WK9BcWrvGW35tL8";

    function __construct()
    {
        // Configure the Google Client.
        $client = new \Google_Client();
        $client->setApplicationName('Google Sheets API');
        $client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
        $client->setAccessType('offline');
        $path = storage_path('app/google/credentials.json');
        $client->setAuthConfig($path);

        // Configure the Sheets Service.
        $this->service = new \Google_Service_Sheets($client);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('inventory.inventory.index', getIndexData(
            $this->title,
            array(
                'inventories' => $this->getAllInventory()
            )
        ));
    }

    /**
     * Get all inventory from Spreadsheet.
     */
    private function getAllInventory()
    {
        $range = 'Sheet2!B3:H';
        $response = $this->service->spreadsheets_values->get(self::SHEET_ID, $range);
        $values = $response->getValues();
        return $values;
    }

    /**
     * Get an inventory from Spreadsheet.
     */
    public function getZeroStockInventory()
    {
        $all = $this->getAllInventory();
        $zeroStocks = [];
        foreach ($all as $item) {
            if (intval($item[2]) < 1)
                $zeroStocks[] = $item[1];
        }
        return $zeroStocks;
    }

    public function getStock($batteryName)
    {
        $all = $this->getAllInventory();
        foreach ($all as $item) {
            if ($item[1] == $batteryName)
                return $item[2];
        }
        return "-";
    }
}
