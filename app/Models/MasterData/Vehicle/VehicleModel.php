<?php

namespace App\Models\MasterData\Vehicle;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// MODELS
use App\Models\MasterData\Battery\BatteryModel;
use App\Models\MasterData\Customer\CustomerModel;

class VehicleModel extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vehicles';

    /**
     * The list of columns in the associated table.
     */
    private static $selectColumns = ['id', 'name', 'brand_id', 'url'];

    /**
     * Get vehicle brand.
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(VehicleBrandModel::class, 'brand_id');
    }

    /**
     * Get all of the customers who own the vehicle.
     */
    public function customers()
    {
        return $this->belongsToMany(CustomerModel::class, 'customer_vehicle', 'vehicle_id', 'customer_id')
            ->withTimestamps();
    }

    /**
     * Get all of the batteries suitable for the vehicle.
     */
    public function batteries()
    {
        return $this->belongsToMany(BatteryModel::class, 'vehicle_battery', 'vehicle_id', 'battery_id')
            ->withTimestamps();
    }

    /**
     * Get all data for DataTables.
     * 
     * @param int $start The starting index of rows.
     * @param int $length The number of rows to be returned.
     * @param string $searchValue The search filter value.
     * @param int $orderColumn The column index for ordering.
     * @param int $orderDirection Ascending or descending order.
     * @return array Associative array containing data for DataTables display.
     */
    public static function allForDataTables($start, $length, $searchValue, $orderColumn, $orderDirection)
    {
        $query = self::query();
        $query->select(self::$selectColumns);

        // Searching process.
        if ($searchValue != null) {
            $query->where(function ($query) use ($searchValue) {
                foreach (self::$selectColumns as $column) {
                    $query->orWhere($column, "LIKE", "%" . $searchValue . "%");
                }
            });
        }

        // Ordering process.
        if ($orderColumn !== null) {
            $columnName = self::$selectColumns[$orderColumn] ?? null;
            if ($columnName !== null) {
                $query->orderBy($columnName, $orderDirection);
            }
        }

        return array(
            "count" => $query->count(),
            "row" => $query->orderBy("name", "ASC")
                ->skip($start)
                ->take($length)
                ->get(),
        );
    }
}
