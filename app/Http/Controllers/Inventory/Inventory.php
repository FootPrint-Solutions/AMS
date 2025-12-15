<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Battery\BatteryCodeModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\Inventory\InventoryDetailModel;

class Inventory extends Controller
{
    private $title = "Inventory";
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
            return $this->stockList[$index][6];
        return "-";
    }

    public function showDetails(Request $request)
    {
        // Get all DataTables requests.
        $draw = $request->input('draw');
        $start = $request->input('start');

        $data = InventoryDetailModel::allForDataTables($request);

        $rows = [];
        $no = $start + 1;
        foreach ($data["row"] as $key) {
            $row = [];
            $row[] = $no++; // #
            $row[] = $key->id; // ID
            $row[] = $key->inventory_id; // Inventory ID
            $row[] = $key->battery_id; // Battery ID
            $row[] = $key->type; // Type
            $row[] = $key->reference; // Reference
            $row[] = $key->quantity; // Quantity
            $row[] = $key->sold; // Sold
            $row[] = $key->sold_at; // Sold At
            $row[] = $key->note; // Note
            $row[] = $key->created_at; // Created At
            $row[] = $key->updated_at; // Updated At
            $row[] = $key->reference_id; // Reference ID
            $row[] = $key->reference_type; // Reference Type
            $rows[] = $row;
        }

        return response()->json(array(
            "draw" => $draw,
            "recordsTotal" => InventoryDetailModel::count(),
            "recordsFiltered" => $data["count"],
            "data" => $rows
        ));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return string
     */
    public function show(Request $request)
    {
        // Get all DataTables requests.
        $draw = $request->input('draw');
        $start = $request->input('start');

        // Get battery brand data (rows and count).
        $data = InventoryDetailModel::allForDataTables($request);

        // Set rows to be displayed in battery brand table.
        $rows = [];
        $no = $start + 1;
        foreach ($data["row"] as $key) {

            if ($key->reference === 'Sales Order Battery') {
                if ($key->type === 'recycle') {
                    $date = isset($key->salesOrderBattery->salesOrder) ? formatDate($key->salesOrderBattery->salesOrder->date) : '-';

                    if ($key->salesOrderBattery->salesOrder->type->trashed()) {
                        $orderNumber = ($key->salesOrderBattery->salesOrder->sales_order_number ?? '-') . ' (Was Deleted)';
                    } else {
                        $orderNumber = $key->salesOrderBattery->salesOrder->sales_order_number ?? '-';
                    }

                    if ($key->battery && $key->battery->trashed()) {
                        $battery = ($key->battery->name ?? '-') . ' (Was Deleted)';
                    } else {
                        $battery = $key->battery->name ?? '-';
                    }

                    $batteryPrice = isset($key->salesOrderBattery) ? formatPrice($key->salesOrderBattery->price_net) : '-';
                    $batteryProductionCode = $key->salesOrderBattery->battery_production_code ?? '-';

                    if ($key->salesOrderBattery->salesOrder->type->trashed()) {
                        $vendor = ($key->salesOrderBattery->salesOrder->vendorData->name ?? '-') . ' (Was Deleted)';
                        $distributorShop = ($key->salesOrderBattery->salesOrder->shipToData->name ?? '-') . ' (Was Deleted)';
                    } else {
                        $vendor = $key->salesOrderBattery->salesOrder->vendorData->name ?? '-';
                        $distributorShop = $key->salesOrderBattery->salesOrder->shipToData->name ?? '-';
                    }
                } else {
                    $date = isset($key->salesOrderBattery->salesOrder) ? formatDate($key->salesOrderBattery->salesOrder->date) : '-';

                    if ($key->salesOrderBattery->salesOrder->customer->trashed()) {
                        $orderNumber = ($key->salesOrderBattery->salesOrder->sales_order_number ?? '-') . ' (Was Deleted)';
                    } else {
                        $orderNumber = $key->salesOrderBattery->salesOrder->sales_order_number ?? '-';
                    }

                    if ($key->battery && $key->battery->trashed()) {
                        $battery = ($key->battery->name ?? '-') . ' (Was Deleted)';
                    } else {
                        $battery = $key->battery->name ?? '-';
                    }
                    $batteryPrice = isset($key->salesOrderBattery) ? formatPrice($key->salesOrderBattery->price_net) : '-';
                    $batteryProductionCode = $key->salesOrderBattery->battery_production_code ?? '-';

                    if ($key->salesOrderBattery->salesOrder->customer->trashed()) {
                        $vendor = ($key->salesOrderBattery->salesOrder->customer->name ?? '-') . ' (Was Deleted)';
                        $distributorShop = ($key->salesOrderBattery->salesOrder->distributorShop->name ?? '-') . ' (Was Deleted)';
                    } else {
                        $vendor = $key->salesOrderBattery->salesOrder->customer->name ?? '-';
                        $distributorShop = $key->salesOrderBattery->salesOrder->distributorShop->name ?? '-';
                    }
                }
            } elseif ($key->reference === 'Purchase Order' || $key->reference === 'purchase_order') {
                $date = isset($key->purchaseOrder) ? formatDate($key->purchaseOrder->date) : '-';
                $orderNumber = $key->purchaseOrder->purchase_order_number ?? '-';
                $distributorShop = $key->purchaseOrder->ship_to->name ?? '-';
                $battery = $key->battery->name ?? '-';
                $batteryPrice = isset($key->purchaseOrder) ? formatPrice($key->purchaseOrder->batteries->firstWhere('battery_id', $key->battery_id)->price_net ?? '0') : '0';
                $batteryProductionCode = isset($key->purchaseOrder) ? $key->purchaseOrder->batteries->firstWhere('battery_id', $key->battery_id)->battery_production_code ?? '-' : '-';

                $vendor = $key->purchaseOrder->supplier->name ?? '-';
            } else {
                $date = '-';
                $orderNumber = '-';
                $distributorShop = '-';
                $battery = '-';
                $batteryPrice = '-';
                $batteryProductionCode = '-';
            }

            $row = [];
            $row[] = $no++;
            $row[] = $date;
            $row[] = $orderNumber;
            $row[] = $vendor;
            $row[] = $distributorShop;
            $row[] = $battery;
            $row[] = $batteryProductionCode;

            if ($key->type === 'in') {
                $row[] = '<span style="color:green;"><i class="fas fa-arrow-down"></i> IN</span>';
            } elseif ($key->type === 'out') {
                $row[] = '<span style="color:red;"><i class="fas fa-arrow-up"></i> OUT</span>';
            } elseif ($key->type === 'adjustment') {
                $row[] = '<span style="color:orange;"><i class="fas fa-exchange-alt"></i> ADJ</span>';
            } else {
                $row[] = '-';
            }
            $row[] = $key->quantity ?? '-';
            $row[] = $batteryPrice;
            $row[] = $key->id ?? '-';
            $row[] = $key->sold ?? '-';
            $rows[] = $row;
        }

        return response()->json(array(
            "draw" => $draw,
            "recordsTotal" => InventoryDetailModel::count(),
            "recordsFiltered" => $data["count"],
            "data" => $rows
        ));
    }
}
