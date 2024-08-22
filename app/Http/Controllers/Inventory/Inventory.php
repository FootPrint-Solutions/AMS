<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Battery\BatteryCodeModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class Inventory extends Controller
{
    private $title = "Battery";
    private $service;
    private $stockList = [];

    // Constant sheet id.
    const SHEET_ID = "1XfqdPabl5RhMNi7MnIvsGgOaS5f7cws3KdCgFhiERQg";

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

        // Set stock list.
        $this->setStockList();
    }

    /**
     * Set current stock list.
     */
    private function setStockList()
    {
        $this->stockList = Cache::remember('stockList', 600, function () {
            return $this->getAllInventory();
        });

        // Insert into inventories table.
        // foreach ($this->stockList as $item) {
        //     $code = BatteryCodeModel::where('code', $item[0]);

        //     if ($code) {
        //         $inventory = Inventory::firstOrNew(['code' => $code->code]);
        //         $inventory->battery_id = $code->battery_id;
        //         $inventory->stock = $item[6];
        //         $inventory->save();
        //     }
        // }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->setStockList();
        return view('Inventory.inventory.index', getIndexData(
            $this->title,
            array(
                'inventories' => $this->stockList
            )
        ));
    }

    /**
     * Get all inventory from Spreadsheet.
     */
    private function getAllInventory()
    {
        $range = 'Sheet1!B3:H';
        $response = $this->service->spreadsheets_values->get(self::SHEET_ID, $range);
        $values = $response->getValues();
        return $values;
    }

    /**
     * Get all zero stock from inventory.
     */
    public function getZeroStockInventory()
    {
        if ($this->stockList == [])
            $this->stockList = $this->getAllInventory();

        $zeroStocks = [];
        foreach ($this->stockList as $item) {
            if (intval($item[6]) < 1)
                $zeroStocks[] = $item[0];
        }
        return $zeroStocks;
    }

    /**
     * Get all non zero stock from inventory.
     */
    public function getNonZeroStockInventory()
    {
        if ($this->stockList == [])
            $this->stockList = $this->getAllInventory();

        $nonZeroStocks = [];
        foreach ($this->stockList as $item) {
            if (intval($item[6]) > 0)
                $nonZeroStocks[] = $item[0];
        }
        return $nonZeroStocks;
    }

    /**
     * Get stock of an inventory based on battery code.
     */
    public function getStock($batteryCode)
    {
        if ($this->stockList == [])
            $this->stockList = $this->getAllInventory();

        $index = array_search($batteryCode, array_column($this->stockList, 0));
        if ($index !== false)
            return $this->stockList[$index][2];
        return "-";
    }
}
