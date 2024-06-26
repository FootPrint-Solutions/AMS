<?php

namespace App\Models\MasterData\Vehicle;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

// MODELS
use App\Models\MasterData\Battery\BatteryModel;
use App\Models\MasterData\Battery\BatterySizeCategoryModel;
use App\Models\MasterData\Customer\CustomerModel;

// TRAITS
use App\Traits\DataTablesTrait;
use OwenIt\Auditing\Auditable as AuditableTrait;

class VehicleModel extends Model implements Auditable
{
    use HasFactory, SoftDeletes, DataTablesTrait, AuditableTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vehicles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'brand_id', 'url'];

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
     * Get all of the battery size categories suitable for the vehicle.
     */
    public function batterySizeCategories()
    {
        return $this->belongsToMany(BatterySizeCategoryModel::class, 'vehicle_battery_size_category', 'vehicle_id', 'battery_size_category_id')
            ->withTimestamps();
    }

    /**
     * Get all data for DataTables.
     * 
     * @param \Illuminate\Http\Request $request The POST request obtained (for DataTables configuration).
     * @return array Associative array containing data for DataTables display.
     */
    public static function allForDataTables($request)
    {
        // Set the list of select and search columns.
        $selectColumns = ['id', 'name', 'brand_id', 'url', 'status'];
        $searchColumns = ['name'];

        // Build the query to obtain all rows.
        $query = self::query();
        $query->select($selectColumns);

        return self::getAllRows($request, $query, $selectColumns, $searchColumns);
    }

    public static function getBatteryRecomendationWithDistributor($ids, $distributor_id)
    {
        return self::whereIn('vehicles.id', $ids)
            ->join('vehicle_battery_size_category', 'vehicles.id', '=', 'vehicle_battery_size_category.vehicle_id')
            ->join('batteries', 'vehicle_battery_size_category.battery_size_category_id', '=', 'batteries.size_category_id')
            ->join('battery_size_categories', 'vehicle_battery_size_category.battery_size_category_id', '=', 'battery_size_categories.id')
            ->join('distributor_shop_battery', 'batteries.id', '=', 'distributor_shop_battery.battery_id', 'left')
            ->join('battery_prices', 'battery_prices.battery_id', '=', 'batteries.id', 'left')
            ->where('distributor_shop_battery.distributor_shop_id', $distributor_id)
            ->where('batteries.deleted_at', null)
            ->where('batteries.status', 1)
            ->select('vehicles.id', 'batteries.id AS battery_id', 'batteries.name', 'batteries.image', 'batteries.warranty', 'batteries.price_retail', 'distributor_shop_battery.battery_id as battery_distributor_id', 'distributor_shop_battery.price as battery_distributor_price', 'distributor_shop_battery.url as battery_distributor_link', 'battery_size_categories.name as size_category', 'batteries.dimension_length', 'batteries.dimension_width', 'batteries.dimension_height', 'batteries.standard_cca', 'batteries.capacity', 'battery_prices.discount', 'battery_prices.price_net', 'battery_prices.price_retail as price_retail_original', 'battery_prices.discount')
            ->get();
    }

    public static function getBatteryRecomendationWithOutDistributor($ids)
    {
        return self::whereIn('vehicles.id', $ids)
            ->where('batteries.deleted_at', null)
            ->where('batteries.status', 1)
            ->join('vehicle_battery_size_category', 'vehicles.id', '=', 'vehicle_battery_size_category.vehicle_id')
            ->leftjoin('battery_size_categories', 'vehicle_battery_size_category.battery_size_category_id', '=', 'battery_size_categories.id')
            ->join('batteries', 'vehicle_battery_size_category.battery_size_category_id', '=', 'batteries.size_category_id')
            ->join('battery_prices', 'battery_prices.battery_id', '=', 'batteries.id', 'left')
            ->select('batteries.id', 'batteries.id AS battery_id', 'batteries.name', 'batteries.image', 'batteries.warranty', 'batteries.price_retail', 'battery_size_categories.name as size_category', 'batteries.dimension_length', 'batteries.dimension_width', 'batteries.dimension_height', 'batteries.standard_cca', 'batteries.capacity',  'battery_prices.price_net', 'battery_prices.price_retail as price_retail_original', 'battery_prices.discount')
            ->get();
    }

    public static function FindSubBattery($ids)
    {
        return self::where('vehicles.id', $ids)
            ->join('vehicle_battery_size_category', 'vehicles.id', '=', 'vehicle_battery_size_category.vehicle_id')
            ->leftjoin('battery_size_categories', 'vehicle_battery_size_category.battery_size_category_id', '=', 'battery_size_categories.id')
            ->select('battery_size_categories.id', 'battery_size_categories.name as size_category')
            ->get();
    }
}
