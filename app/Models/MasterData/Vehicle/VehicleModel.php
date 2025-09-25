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
use App\Models\MasterData\Vehicle\VehicleFuelModel;
use App\Models\MasterData\Vehicle\VehicleTransmissionModel;
use App\Models\MasterData\Vehicle\VehicleYearModel;

// TRAITS
use App\Traits\DataTablesTrait;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Illuminate\Support\Facades\DB;

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
    protected $fillable = ['name', 'brand_id', 'url', 'note', 'status', 'vehicle_years_id', 'vehicle_fuels_id', 'vehicle_transmissions_id'];

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
     * Get the vehicle year.
     */
    public function year(): BelongsTo
    {
        return $this->belongsTo(VehicleYearModel::class, 'vehicle_years_id');
    }

    /**
     * Get the vehicle fuel type.
     */
    public function fuelVehicle(): BelongsTo
    {
        return $this->belongsTo(VehicleFuelModel::class, 'vehicle_fuels_id');
    }

    /**
     * Get the vehicle transmission type.
     */
    public function transmission(): BelongsTo
    {
        return $this->belongsTo(VehicleTransmissionModel::class, 'vehicle_transmissions_id');
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
        $selectColumns = ['id', 'name', 'brand_id', 'url', 'note', 'status', 'vehicle_years_id'];
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
            ->leftJoin('battery_codes', 'batteries.id', '=', 'battery_codes.battery_id')
            ->where('distributor_shop_battery.distributor_shop_id', $distributor_id)
            ->where('batteries.deleted_at', null)
            ->where('batteries.status', 1)
            ->select('vehicles.id', 'batteries.id AS battery_id', 'batteries.name', 'batteries.image', 'batteries.warranty', 'batteries.price_retail', 'distributor_shop_battery.battery_id as battery_distributor_id', 'distributor_shop_battery.price as battery_distributor_price', 'distributor_shop_battery.url as battery_distributor_link', 'battery_size_categories.name as size_category', 'batteries.dimension_length', 'batteries.dimension_width', 'batteries.dimension_height', 'batteries.standard_cca', 'batteries.capacity', 'battery_prices.discount', 'battery_prices.price_net', 'battery_prices.price_retail as price_retail_original', 'battery_prices.discount', 'battery_codes.code')
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
            ->leftJoin('battery_codes', 'batteries.id', '=', 'battery_codes.battery_id')
            ->select('batteries.id', 'batteries.id AS battery_id', 'batteries.name', 'batteries.image', 'batteries.warranty', 'batteries.price_retail', 'battery_size_categories.name as size_category', 'batteries.dimension_length', 'batteries.dimension_width', 'batteries.dimension_height', 'batteries.standard_cca', 'batteries.capacity',  'battery_prices.price_net', 'battery_prices.price_retail as price_retail_original', 'battery_prices.discount', 'battery_codes.code', 'battery_prices.discount_price')
            ->get();
    }

    public static function getBatteryRecomendationWithCategory($ids)
    {
        return self::whereIn('batteries.id', $ids)
            ->distinct('batteries.id')
            ->where('batteries.deleted_at', null)
            ->where('batteries.status', 1)
            ->join('vehicle_battery_size_category', 'vehicles.id', '=', 'vehicle_battery_size_category.vehicle_id')
            ->leftjoin('battery_size_categories', 'vehicle_battery_size_category.battery_size_category_id', '=', 'battery_size_categories.id')
            ->join('batteries', 'vehicle_battery_size_category.battery_size_category_id', '=', 'batteries.size_category_id')
            ->join('battery_prices', 'battery_prices.battery_id', '=', 'batteries.id', 'left')
            ->leftJoin('battery_codes', 'batteries.id', '=', 'battery_codes.battery_id')
            ->select('batteries.id', 'batteries.id AS battery_id', 'batteries.name', 'batteries.image', 'batteries.warranty', 'batteries.price_retail', 'battery_size_categories.name as size_category', 'batteries.dimension_length', 'batteries.dimension_width', 'batteries.dimension_height', 'batteries.standard_cca', 'batteries.capacity',  'battery_prices.price_net', 'battery_prices.price_retail as price_retail_original', 'battery_prices.discount', 'battery_codes.code', 'battery_prices.discount_price')
            ->get();
    }

    public static function getBatteryRecomendationWithCategoryFixed($ids)
    {
        return DB::table('batteries')
            ->select(
                'batteries.id',
                'batteries.id AS battery_id',
                'batteries.name',
                'batteries.image',
                'batteries.warranty',
                'batteries.price_retail',
                'battery_size_categories.name as size_category',
                'batteries.dimension_length',
                'batteries.dimension_width',
                'batteries.dimension_height',
                'batteries.standard_cca',
                'batteries.capacity',
                'battery_prices.price_net',
                'battery_prices.price_retail as price_retail_original',
                'battery_prices.discount',
                'battery_codes.code',
                'battery_prices.discount_price'
            )
            ->leftJoin('battery_size_categories', 'batteries.size_category_id', '=', 'battery_size_categories.id')
            ->leftJoin('battery_prices', 'battery_prices.battery_id', '=', 'batteries.id')
            ->leftJoin('battery_codes', 'batteries.id', '=', 'battery_codes.battery_id')
            ->whereIn('batteries.id', $ids)
            ->whereNull('batteries.deleted_at')
            ->where('batteries.status', 1)
            ->distinct()
            ->get();
    }

    public static function getBatteryRecomendationWithCategoryFix($ids)
    {
        return DB::table('batteries')
            ->whereIn('batteries.id', $ids)
            ->whereNull('batteries.deleted_at')
            ->where('batteries.status', 1)
            ->leftJoin('battery_size_categories', 'batteries.size_category_id', '=', 'battery_size_categories.id')
            ->leftJoin('battery_prices', 'battery_prices.battery_id', '=', 'batteries.id')
            ->leftJoin('battery_codes', 'batteries.id', '=', 'battery_codes.battery_id')
            ->select(
                'batteries.id',
                'batteries.id AS battery_id',
                'batteries.name',
                'batteries.image',
                'batteries.warranty',
                'batteries.price_retail',
                'battery_size_categories.name as size_category',
                'batteries.dimension_length',
                'batteries.dimension_width',
                'batteries.dimension_height',
                'batteries.standard_cca',
                'batteries.capacity',
                'battery_prices.price_net',
                'battery_prices.price_retail as price_retail_original',
                'battery_prices.discount',
                'battery_codes.code',
                'battery_prices.discount_price'
            )
            ->distinct()
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

    public static function getBatteryRecomendationWithCategoryAll($category)
    {
        return DB::table('batteries')
            ->whereNull('batteries.deleted_at')
            ->where('batteries.status', 1)
            ->leftJoin('battery_size_categories', 'batteries.size_category_id', '=', 'battery_size_categories.id')
            ->leftJoin('battery_prices', 'battery_prices.battery_id', '=', 'batteries.id')
            ->leftJoin('battery_codes', 'batteries.id', '=', 'battery_codes.battery_id')
            ->select(
                'batteries.id',
                'batteries.id AS battery_id',
                'batteries.name',
                'batteries.image',
                'batteries.warranty',
                'batteries.price_retail',
                'battery_size_categories.name as size_category',
                'batteries.dimension_length',
                'batteries.dimension_width',
                'batteries.dimension_height',
                'batteries.standard_cca',
                'batteries.capacity',
                'battery_prices.price_net',
                'battery_prices.price_retail as price_retail_original',
                'battery_prices.discount',
                'battery_codes.code',
                'battery_prices.discount_price'
            )
            ->where('battery_size_categories.id', $category)
            ->distinct()
            ->get();
    }

    public static function getBatteryRecomendationWithCategoryAndCca($cca)
    {
        return DB::table('batteries')
            ->whereNull('batteries.deleted_at')
            ->where('batteries.status', 1)
            ->leftJoin('battery_size_categories', 'batteries.size_category_id', '=', 'battery_size_categories.id')
            ->leftJoin('battery_prices', 'battery_prices.battery_id', '=', 'batteries.id')
            ->leftJoin('battery_codes', 'batteries.id', '=', 'battery_codes.battery_id')
            ->select(
                'batteries.id',
                'batteries.id AS battery_id',
                'batteries.name',
                'batteries.image',
                'batteries.warranty',
                'batteries.price_retail',
                'battery_size_categories.name as size_category',
                'batteries.dimension_length',
                'batteries.dimension_width',
                'batteries.dimension_height',
                'batteries.standard_cca',
                'batteries.capacity',
                'battery_prices.price_net',
                'battery_prices.price_retail as price_retail_original',
                'battery_prices.discount',
                'battery_codes.code',
                'battery_prices.discount_price'
            )
            ->where('batteries.standard_cca', $cca)
            ->distinct()
            ->get();
    }

    public static function getBatteryRecomendationWithCategoryAndCapacity($capacity)
    {
        return DB::table('batteries')
            ->whereNull('batteries.deleted_at')
            ->where('batteries.status', 1)
            ->leftJoin('battery_size_categories', 'batteries.size_category_id', '=', 'battery_size_categories.id')
            ->leftJoin('battery_prices', 'battery_prices.battery_id', '=', 'batteries.id')
            ->leftJoin('battery_codes', 'batteries.id', '=', 'battery_codes.battery_id')
            ->select(
                'batteries.id',
                'batteries.id AS battery_id',
                'batteries.name',
                'batteries.image',
                'batteries.warranty',
                'batteries.price_retail',
                'battery_size_categories.name as size_category',
                'batteries.dimension_length',
                'batteries.dimension_width',
                'batteries.dimension_height',
                'batteries.standard_cca',
                'batteries.capacity',
                'battery_prices.price_net',
                'battery_prices.price_retail as price_retail_original',
                'battery_prices.discount',
                'battery_codes.code',
                'battery_prices.discount_price'
            )
            ->where('batteries.capacity', $capacity)
            ->distinct()
            ->get();
    }

    public static function getBatteryRecomendationWithCategoryAndDimension($dimension)
    {
        $dimension = explode(',', $dimension);
        return DB::table('batteries')
            ->whereNull('batteries.deleted_at')
            ->where('batteries.status', 1)
            ->leftJoin('battery_size_categories', 'batteries.size_category_id', '=', 'battery_size_categories.id')
            ->leftJoin('battery_prices', 'battery_prices.battery_id', '=', 'batteries.id')
            ->leftJoin('battery_codes', 'batteries.id', '=', 'battery_codes.battery_id')
            ->select(
                'batteries.id',
                'batteries.id AS battery_id',
                'batteries.name',
                'batteries.image',
                'batteries.warranty',
                'batteries.price_retail',
                'battery_size_categories.name as size_category',
                'batteries.dimension_length',
                'batteries.dimension_width',
                'batteries.dimension_height',
                'batteries.standard_cca',
                'batteries.capacity',
                'battery_prices.price_net',
                'battery_prices.price_retail as price_retail_original',
                'battery_prices.discount',
                'battery_codes.code',
                'battery_prices.discount_price'
            )
            ->where('batteries.dimension_length', $dimension[0])
            ->where('batteries.dimension_width', $dimension[1])
            ->where('batteries.dimension_height', $dimension[2])
            ->distinct()
            ->get();
    }
}
