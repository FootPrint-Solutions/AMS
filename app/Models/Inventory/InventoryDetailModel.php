<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

// TRAITS
use App\Traits\DataTablesTrait;
use OwenIt\Auditing\Auditable as AuditableTrait;

use App\Models\MasterData\Battery\BatteryModel;
use App\Models\Inventory\InventoryModel;
use App\Models\MasterData\Distributor\DistributorShopModel;

class InventoryDetailModel extends Model implements Auditable
{
    use HasFactory, DataTablesTrait, AuditableTrait;

    protected $table = 'inventory_details';

    protected $fillable = [
        'inventory_id',
        'distributor_shop_id',
        'battery_id',
        'type',
        'reference',
        'quantity',
        'sold',
        'sold_at',
        'note',
        'reference_id',
        'reference_type',
    ];

    // Relationship dengan Inventory
    public function inventory()
    {
        return $this->belongsTo(InventoryModel::class, 'inventory_id');
    }

    // Relationship dengan Battery
    public function battery()
    {
        return $this->belongsTo(BatteryModel::class, 'battery_id');
    }

    // Morph relationship reference
    public function reference()
    {
        return $this->morphTo();
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
            'type',
            'reference',
            'quantity',
            'sold',
            'sold_at',
            'note',
            'created_at',
            'updated_at',
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
            'sold',
            'sold_at',
            'note',
            'created_at',
            'updated_at',
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
