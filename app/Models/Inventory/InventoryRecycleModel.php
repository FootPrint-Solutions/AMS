<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

// TRAITS
use App\Traits\DataTablesTrait;
use OwenIt\Auditing\Auditable as AuditableTrait;

// MODELS
use App\Models\MasterData\Battery\BatteryModel;
use App\Models\MasterData\Battery\BatteryRecycleModel;

class InventoryRecycleModel extends Model implements Auditable
{
    use HasFactory, DataTablesTrait, AuditableTrait;

    protected $table = 'inventory_recycles';
    protected $primaryKey = 'id';

    protected $fillable = [
        'battery_id',
        'battery_recycle_id',
        'code',
        'stock',
    ];

    protected $casts = [
        'stock' => 'double',
    ];

    public $timestamps = true;

    // Relationships
    public function battery()
    {
        return $this->belongsTo(BatteryModel::class, 'battery_id', 'id');
    }

    public function batteryRecycle()
    {
        return $this->belongsTo(BatteryRecycleModel::class, 'battery_recycle_id', 'id');
    }

    /**
     * Get all data for DataTables.
     */
    public static function allForDataTables($request)
    {
        $selectColumns = ['id', 'battery_id', 'battery_recycle_id', 'code', 'stock'];
        $searchColumns = ['battery_id', 'battery_recycle_id', 'code', 'stock'];

        $query = self::query();
        $query->select($selectColumns)->with(['battery', 'batteryRecycle']);

        return self::getAllRows($request, $query, $selectColumns, $searchColumns);
    }
}
