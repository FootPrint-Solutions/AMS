<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

// TRAITS
use App\Traits\DataTablesTrait;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Illuminate\Support\Facades\Log;

use App\Models\MasterData\Battery\BatteryModel;
use App\Models\MasterData\Battery\BatteryRecycleModel;
use App\Models\Inventory\InventoryRecycleModel;
use App\Models\Orders\SalesOrder\SalesOrderModel;
use App\Models\Orders\SalesOrder\SalesOrderBatteryModel;
use App\Models\MasterData\Distributor\DistributorShopModel;
use App\Models\Orders\PurchaseOrder\PurchaseOrderModel;

class InventoryRecycleDetailModel extends Model implements Auditable
{
    use HasFactory, DataTablesTrait, AuditableTrait;

    protected $table = 'inventory_recycle_details';

    protected $fillable = [
        'inventory_id',
        'distributor_shop_id',
        'battery_id',
        'battery_recycle_id',
        'type',
        'reference',
        'quantity',
        'sold',
        'sold_at',
        'note',
        'reference_id',
        'reference_type',
    ];

    // Relationship dengan InventoryRecycle
    public function inventoryRecycle()
    {
        return $this->belongsTo(InventoryRecycleModel::class, 'inventory_id');
    }

    // Relationship dengan Battery
    public function battery()
    {
        return $this->belongsTo(BatteryModel::class, 'battery_id')->withTrashed();
    }

    // Relationship dengan BatteryRecycle
    public function batteryRecycle()
    {
        return $this->belongsTo(BatteryRecycleModel::class, 'battery_recycle_id')->withTrashed();
    }

    // Morph relationship reference
    public function reference()
    {
        return $this->morphTo();
    }

    // Relationship dengan SalesOrderBattery
    public function salesOrderBattery()
    {
        return $this->hasOne(SalesOrderBatteryModel::class, 'id', 'reference_id')->with('salesOrder');
    }

    // Relationship dengan DistributorShop
    public function distributorShop()
    {
        return $this->belongsTo(DistributorShopModel::class, 'distributor_shop_id');
    }

    // Relationship dengan PurchaseOrder
    public function purchaseOrder()
    {
        return $this->hasOne(PurchaseOrderModel::class, 'id', 'reference_id')->with('supplier', 'batteries')->withTrashed();
    }

    public static function allForDataTables($request)
    {
        $start = $request->input("start") ?? 0;
        $length = $request->input("length") ?? 10;
        $searchValue = $request->input("search.value");
        $orderColumn = $request->input("order.0.column");
        $orderDirection = $request->input("order.0.dir");

        $selectColumns = [
            'id',
            'inventory_id',
            'battery_id',
            'battery_recycle_id',
            'type',
            'reference',
            'quantity',
            'note',
            'reference_id',
            'reference_type',
            'sold',
            'sold_at',
            'distributor_shop_id',
        ];

        $searchColumns = [
            'id',
            'inventory_id',
            'battery_id',
            'battery_recycle_id',
            'type',
            'reference',
            'quantity',
            'note',
            'reference_id',
            'reference_type',
        ];

        $orderDefault = [
            "column" => "updated_at",
            "direction" => "desc"
        ];

        $query = self::query();
        $query->select($selectColumns);

        $query->with([
            'inventoryRecycle',
            'battery',
            'batteryRecycle',
            'salesOrderBattery',
            'purchaseOrder',
            'distributorShop'
        ]);

        $query->withCount([
            'inventoryRecycle',
            'battery',
            'batteryRecycle',
            'salesOrderBattery',
            'purchaseOrder',
            'distributorShop'
        ]);

        // Searching process.
        if ($searchColumns != null && $searchValue != null) {
            $query->where(function ($query) use ($searchValue, $searchColumns) {
                foreach ($searchColumns as $column) {
                    $query->orWhere($column, "LIKE", "%" . $searchValue . "%");
                }

                $query->orWhereHas('battery', function ($query) use ($searchValue) {
                    $query->where('name', 'LIKE', "%" . $searchValue . "%");
                });

                $query->orWhereHas('batteryRecycle', function ($query) use ($searchValue) {
                    $query->where('name', 'LIKE', "%" . $searchValue . "%");
                });

                $query->orWhereHas('salesOrderBattery', function ($query) use ($searchValue) {
                    $query->whereHas('salesOrder', function ($query) use ($searchValue) {
                        $query->where('sales_order_number', 'LIKE', "%" . $searchValue . "%")
                            ->orWhere('price_net', 'LIKE', "%" . $searchValue . "%")
                            ->orWhere('battery_production_code', 'LIKE', "%" . $searchValue . "%");
                    });
                });

                $query->orWhereHas('salesOrderBattery.salesOrder.customer', function ($query) use ($searchValue) {
                    $query->where('name', 'LIKE', "%" . $searchValue . "%");
                });

                $query->orWhereHas('distributorShop', function ($query) use ($searchValue) {
                    $query->where('name', 'LIKE', "%" . $searchValue . "%");
                });

                $query->orWhereHas('purchaseOrder', function ($query) use ($searchValue) {
                    $query->where('purchase_order_number', 'LIKE', "%" . $searchValue . "%");
                });

                $query->orWhereHas('salesOrderBattery.salesOrder.vendorData', function ($query) use ($searchValue) {
                    $query->where('name', 'LIKE', "%" . $searchValue . "%");
                });

                $query->orWhereHas('salesOrderBattery.salesOrder.shipToData', function ($query) use ($searchValue) {
                    $query->where('name', 'LIKE', "%" . $searchValue . "%");
                });

                $query->orWhereHas('purchaseOrder', function ($query) use ($searchValue) {
                    $query->where('purchase_order_number', 'LIKE', "%" . $searchValue . "%");
                });

                $query->orWhereHas('purchaseOrder.vendor', function ($query) use ($searchValue) {
                    $query->where('name', 'LIKE', "%" . $searchValue . "%");
                });

                $query->orWhereHas('purchaseOrder.shipTo', function ($query) use ($searchValue) {
                    $query->where('name', 'LIKE', "%" . $searchValue . "%");
                });
            });
        }

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

        if ($request->inventory_id) {
            $query->where('inventory_id', $request->inventory_id);
        }

        // Ordering process.
        if ($orderColumn !== null) {
            $columnName = $selectColumns[$orderColumn] ?? null;
            if ($columnName !== null) {
                $query->orderBy($columnName, $orderDirection);
            }
        } else {
            if ($orderDefault !== null) {
                $query->orderBy($orderDefault["column"], $orderDefault["direction"]);
            } else {
                $query->orderBy("updated_at", "desc");
            }
        }

        return array(
            "count" => $query->count(),
            "row" => $query->skip($start)
                ->take($length)
                ->get(),
        );
    }
}
