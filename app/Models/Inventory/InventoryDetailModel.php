<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;


// TRAITS
use App\Traits\DataTablesTrait;
use OwenIt\Auditing\Auditable as AuditableTrait;

use App\Models\MasterData\Battery\BatteryModel;
use App\Models\Inventory\InventoryRecycleModel;
use App\Models\Orders\SalesOrder\SalesOrderModel;
use App\Models\Orders\SalesOrder\SalesOrderBatteryModel;

class InventoryDetailModel extends Model implements Auditable
{
    use HasFactory, DataTablesTrait, AuditableTrait;

    protected $table = 'inventory_details';

    protected $fillable = [
        'inventory_id',
        'battery_id',
        'type',
        'reference',
        'quantity',
        'note',
        'reference_id',
        'reference_type',
    ];

    // Define the relationship with the Inventory model
    public function inventory()
    {
        return $this->belongsTo(InventoryModel::class, 'inventory_id');
    }

    public function reference()
    {
        return $this->morphTo();
    }

    // Define the relationship with the Battery model
    public function battery()
    {
        return $this->belongsTo(BatteryModel::class, 'battery_id');
    }

    // Define the relationship with the InventoryRecycle model
    public function inventoryRecycle()
    {
        return $this->hasOne(InventoryRecycleModel::class, 'id', 'inventory_id');
    }

    // Define the relationship with the SalesOrderBattery model
    public function salesOrderBattery()
    {
        return $this->hasOne(SalesOrderBatteryModel::class, 'id', 'reference_id')->with('salesOrder');
    }

    public static function allForDataTables($request)
    {
        // Get DataTables configuration request.
        $start = $request->input("start");
        $length = $request->input("length");
        $searchValue = $request->input("search.value");
        $orderColumn = $request->input("order.0.column");
        $orderDirection = $request->input("order.0.dir");

        // Set the list of select and search columns.
        $selectColumns = [
            'id',
            'inventory_id',
            'battery_id',
            'type',
            'reference',
            'quantity',
            'note',
            'reference_id',
            'reference_type',
        ];

        $searchColumns = [
            'id',
            'inventory_id',
            'battery_id',
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
            'inventory',
            'battery',
            'inventoryRecycle',
            'salesOrderBattery'
        ]);
        $query->withCount([
            'inventory',
            'battery',
            'inventoryRecycle',
            'salesOrderBattery'
        ]);

        // Searching process.
        if ($searchColumns != null && $searchValue != null) {
            $query->where(function ($query) use ($searchValue, $searchColumns) {
                foreach ($searchColumns as $column) {
                    $query->orWhere($column, "LIKE", "%" . $searchValue . "%");
                }
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
