<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Battery\BatteryCodeModel;
use App\Models\MasterData\Battery\BatteryModel;
use App\Models\MasterData\Battery\BatteryRecycleModel;
use App\Models\MasterData\Distributor\DistributorShopModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\Inventory\InventoryRecycleDetailModel;
use App\Models\Inventory\InventoryRecycleModel;

class InventoryRecycle extends Controller
{
    private $title = "Inventory Recycle";

    public function index()
    {
        return view('Inventory.InventoryRecycle.index', getIndexData(
            $this->title,
        ));
    }

    public function show(Request $request)
    {
        // Get all DataTables requests.
        $draw = $request->input('draw');
        $start = $request->input('start');

        $data = InventoryRecycleModel::allForDataTables($request);

        $rows = [];
        $no = $start + 1;
        foreach ($data["row"] as $key) {
            $row = [];
            $row[] = $no++;
            $row[] = $key->id;
            $row[] = $key->batteryRecycle
                ? ($key->batteryRecycle->name ?? '-')
                : ($key->battery
                    ? ($key->battery->name ?? '-')
                    : 'Battery Deleted');
            $row[] = $key->stock;
            $rows[] = $row;
        }

        return response()->json(array(
            "draw" => $draw,
            "recordsTotal" => InventoryRecycleDetailModel::count(),
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
    public function showDeatails(Request $request)
    {
        // Get all DataTables requests.
        $draw = $request->input('draw');
        $start = $request->input('start');

        // Get battery brand data (rows and count).
        $data = InventoryRecycleDetailModel::allForDataTables($request);

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
                $vendor = '-';
            }

            $row = [];
            $row[] = $no++;
            $row[] = $date;
            $row[] = $orderNumber;
            $row[] = $vendor ?: '-';
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
            "recordsTotal" => InventoryRecycleDetailModel::count(),
            "recordsFiltered" => $data["count"],
            "data" => $rows
        ));
    }

    public function showDetails(Request $request)
    {
        // Get all DataTables requests.
        $draw = $request->input('draw');
        $start = $request->input('start');

        // Get battery brand data (rows and count).
        $data = InventoryRecycleDetailModel::allForDataTables($request);

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
                $vendor = '-';
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
            "recordsTotal" => InventoryRecycleDetailModel::count(),
            "recordsFiltered" => $data["count"],
            "data" => $rows
        ));
    }

    public function syncStock(Request $request)
    {
        // Get all inventories
        $inventories = InventoryRecycleModel::all();
        foreach ($inventories as $inventory) {
            $totalIn = InventoryRecycleDetailModel::where('inventory_id', $inventory->id)
                ->where('type', 'in')
                ->sum('quantity');

            $totalOut = InventoryRecycleDetailModel::where('inventory_id', $inventory->id)
                ->where('type', 'out')
                ->sum('quantity');

            $totalAdjustment = InventoryRecycleDetailModel::where('inventory_id', $inventory->id)
                ->where('type', 'adjustment')
                ->sum('quantity');

            $calculatedStock = $totalIn - abs($totalOut) + $totalAdjustment;
            $inventory->stock = $calculatedStock;
            $inventory->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Stock synchronized successfully'
        ]);
    }

    public function detailsIndex()
    {
        $batteries = BatteryRecycleModel::all();
        $distributorShops = DistributorShopModel::all();

        return view('Inventory.InventoryRecycle.details', array_merge(
            getIndexData('Inventory Details'),
            [
                'batteries' => $batteries,
                'distributorShops' => $distributorShops
            ]
        ));
    }

    public function getTotalQty(Request $request)
    {
        $query = InventoryRecycleDetailModel::query();

        // Apply filters
        if ($request->dateStart && $request->dateEnd) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('salesOrderBattery.salesOrder', function ($query) use ($request) {
                    $query->whereBetween('date', [$request->dateStart, $request->dateEnd]);
                })->orWhereHas('purchaseOrder', function ($query) use ($request) {
                    $query->whereBetween('date', [$request->dateStart, $request->dateEnd]);
                });
            });
        }

        if ($request->orderNumber) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('salesOrderBattery.salesOrder', function ($query) use ($request) {
                    $query->where('sales_order_number', 'LIKE', '%' . $request->orderNumber . '%');
                })->orWhereHas('purchaseOrder', function ($query) use ($request) {
                    $query->where('purchase_order_number', 'LIKE', '%' . $request->orderNumber . '%');
                });
            });
        }

        if ($request->customerSupplier) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('salesOrderBattery.salesOrder.customer', function ($query) use ($request) {
                    $query->where('name', 'LIKE', '%' . $request->customerSupplier . '%');
                })->orWhereHas('purchaseOrder.supplier', function ($query) use ($request) {
                    $query->where('name', 'LIKE', '%' . $request->customerSupplier . '%');
                });
            });
        }

        if ($request->distributorShop) {
            $query->where(function ($q) use ($request) {
                $q->where('distributor_shop_id', $request->distributorShop)
                    ->orWhereHas('salesOrderBattery.salesOrder', function ($query) use ($request) {
                        $query->where('distributor_shop_id', $request->distributorShop);
                    })
                    ->orWhereHas('purchaseOrder', function ($query) use ($request) {
                        $query->where('distributor_shop_id', $request->distributorShop);
                    });
            });
        }

        if ($request->battery) {
            $query->where('battery_id', $request->battery);
        }

        $totalQty = $query->sum('quantity');

        return response()->json([
            'success' => true,
            'totalQty' => $totalQty ?? 0
        ]);
    }
}
