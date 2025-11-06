<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

// TRAITS
use App\Traits\DataTablesTrait;
use OwenIt\Auditing\Auditable as AuditableTrait;

use App\Models\MasterData\Battery\BatteryModel;
use App\Models\MasterData\Battery\BatteryRecycleModel;
use App\Models\Inventory\InventoryRecycleModel;
use App\Models\Orders\SalesOrder\SalesOrderModel;
use App\Models\Orders\SalesOrder\SalesOrderBatteryModel;
use App\Models\MasterData\Distributor\DistributorShopModel;

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
        return $this->belongsTo(BatteryModel::class, 'battery_id');
    }

    // Relationship dengan BatteryRecycle
    public function batteryRecycle()
    {
        return $this->belongsTo(BatteryRecycleModel::class, 'battery_recycle_id');
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

    public static function allForDataTables($request)
    {
        $start = $request->input("start");
        $length = $request->input("length");
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
            'salesOrderBattery'
        ]);

        $query->withCount([
            'inventoryRecycle',
            'battery',
            'batteryRecycle',
            'salesOrderBattery'
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
                    $query->where('recycle_code', 'LIKE', "%" . $searchValue . "%");
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
            });
        }

        if ($request->dateStart && $request->dateEnd) {
            $query->whereHas('salesOrderBattery.salesOrder', function ($query) use ($request) {
                $query->where('date', '>=', $request->dateStart)
                    ->where('date', '<=', $request->dateEnd);
            });
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
