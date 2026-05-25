<?php

namespace App\Models\MasterData\Battery;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Contracts\Auditable;

// TRAITS
use App\Traits\DataTablesTrait;
use Illuminate\Database\Eloquent\Relations\HasOne;
use OwenIt\Auditing\Auditable as AuditableTrait;

// MODELS
use App\Models\MasterData\Battery\BatteryBrandModel;
use App\Models\MasterData\Battery\BatterySubbrandCategoryModel;
use App\Models\MasterData\Battery\BatteryUsageTypeModel;
use App\Models\MasterData\Battery\BatterySizeCategoryModel;
use App\Models\MasterData\Battery\BatteryTechnologyModel;
use App\Models\MasterData\Battery\BatteryUrlModel;
use App\Models\MasterData\Battery\BatteryCodeModel;
use App\Models\MasterData\Battery\BatteryPriceModel;
use App\Models\MasterData\Vehicle\VehicleBatteryModel;
use App\Models\MasterData\Battery\BatteryImageModel;
use App\Models\MasterData\Battery\BatteryRecycleModel;

class BatteryModel extends Model implements Auditable
{
    use HasFactory, SoftDeletes, DataTablesTrait, AuditableTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'batteries';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'brand_id',
        'subbrand_category_id',
        'usage_type_id',
        'size_category_id',
        'technology_id',
        'dimension_length',
        'dimension_width',
        'dimension_height',
        'standard_cca',
        'capacity',
        'warranty',
        'price_retail',
        'price_buy',
        'name_alternate',
        'type',
        'editable_price'
    ];

    /**
     * Get battery urls.
     */
    public function urls(): HasMany
    {
        return $this->hasMany(BatteryUrlModel::class, 'battery_id', 'id');
    }

    /**
     * Get battery brand.
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(BatteryBrandModel::class, 'brand_id');
    }

    /**
     * Get battery subbrand category.
     */
    public function subbrandCategory(): BelongsTo
    {
        return $this->belongsTo(BatterySubbrandCategoryModel::class, 'subbrand_category_id');
    }

    /**
     * Get battery usage type.
     */
    public function usageType(): BelongsTo
    {
        return $this->belongsTo(BatteryUsageTypeModel::class, 'usage_type_id');
    }

    /**
     * Get battery size category.
     */
    public function sizeCategory(): BelongsTo
    {
        return $this->belongsTo(BatterySizeCategoryModel::class, 'size_category_id');
    }

    /**
     * Get battery technology.
     */
    public function technology(): BelongsTo
    {
        return $this->belongsTo(BatteryTechnologyModel::class, 'technology_id');
    }

    /**
     * Get battery code.
     */
    public function code(): HasOne
    {
        return $this->hasOne(BatteryCodeModel::class, 'battery_id', 'id');
    }

    /**
     * Get battery vehicle battery.
     */
    public function vehicleBattery(): BelongsTo
    {
        return $this->belongsTo(VehicleBatteryModel::class, 'id', 'battery_id')
            ->with('vehicle');
    }

    /**
     * Get Battery Urls.
     */
    public function batteryUrl(): HasMany
    {
        return $this->hasMany(BatteryUrlModel::class, 'battery_id', 'id');
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
        $selectColumns = [
            'id',
            'name',
            'brand_id',
            'subbrand_category_id',
            'usage_type_id',
            'size_category_id',
            'technology_id',
            'dimension_length',
            'dimension_width',
            'dimension_height',
            'standard_cca',
            'capacity',
            'warranty',
            'price_retail',
            'price_buy',
            'name_alternate',
            'status',
            'type',
            'battery_codes.code'
        ];
        $searchColumns = [
            'name',
            'name_alternate',
            'status',
            'type',
            'battery_codes.code'
        ];

        // Build the query to obtain all rows.
        $query = self::query();
        $query->select($selectColumns);
        $query->join('battery_codes', 'batteries.id', '=', 'battery_codes.battery_id', 'left');

        if (!empty($request->search['value'])) {
            $searchValue = $request->search['value'];
            $query->where(function ($q) use ($searchColumns, $searchValue) {
                foreach ($searchColumns as $column) {
                    $q->orWhere($column, 'LIKE', '%' . $searchValue . '%');
                }
            });
        }

        if (isset($request->status) && $request->status !== 'all' && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if (isset($request->type) && $request->type !== 'all' && $request->type !== '') {
            $query->where('type', $request->type);
        }

        return self::getAllRows($request, $query, $selectColumns, $searchColumns, ['column' => 'batteries.updated_at', 'direction' => 'desc']);
    }

    /**
     * Get rows for autocomplete.
     * 
     * @param string $keyword The autocomplete keyword.
     * @param array $extraColumn The list of other columns except id and name to obtain.
     * @param array $whereIn The list of to be included battery-only.
     * @param int $limit The limit number of rows returned.
     */
    public static function allForAutocompleteWithRecycle($keyword, $extraColumn, $whereIn = [], $limit = 5)
    {
        $columns = ["batteries.id", "batteries.name"];
        $batteryQuery = self::select(array_merge($columns, $extraColumn))
            ->where("batteries.type", "regular")
            ->where("batteries.name", "like", "%{$keyword}%")
            ->join('battery_prices', 'batteries.id', '=', 'battery_prices.battery_id')
            ->leftJoin('battery_codes', 'batteries.id', '=', 'battery_codes.battery_id');

        if (!empty($whereIn)) {
            $batteryQuery->whereIn('battery_codes.code', $whereIn);
        }

        $batteryResults = $batteryQuery->limit($limit)->get()->toArray();

        // Query for battery_recycles table
        $recycleColumns = ["id", "name", "price"];
        $recycleQuery = DB::table('battery_recycles')
            ->select($recycleColumns)
            ->where('name', 'like', "%{$keyword}%")
            ->where('status', 1);

        // add column type constant 'recycle' to distinguish from batteries table
        $recycleQuery->selectRaw("'recycle' as type");
        $recycleResults = $recycleQuery->limit($limit)->get()->toArray();

        // Merge both results
        return array_merge($batteryResults, $recycleResults);
    }

    /**
     * Get rows for autocomplete.
     * 
     * @param string $keyword The autocomplete keyword.
     * @param array $extraColumn The list of other columns except id and name to obtain.
     * @param array $whereIn The list of to be included battery-only.
     * @param int $limit The limit number of rows returned.
     */
    public static function allForAutocomplete($keyword, $extraColumn, $whereIn = [], $limit = 5)
    {
        $columns = ["batteries.id", "batteries.name"];
        $batteryQuery = self::select(array_merge($columns, $extraColumn))
            ->where('status', 1)
            ->where("batteries.type", "regular")
            ->where("batteries.name", "like", "%{$keyword}%")
            ->join('battery_prices', 'batteries.id', '=', 'battery_prices.battery_id')
            ->leftJoin('battery_codes', 'batteries.id', '=', 'battery_codes.battery_id');

        if (!empty($whereIn)) {
            $batteryQuery->whereIn('battery_codes.code', $whereIn);
        }

        $batteryResults = $batteryQuery->limit($limit)->get()->toArray();

        return $batteryResults;
    }

    public static function getBatteryDistributor($selectedBatteryIds, $distributorShopId)
    {
        $batteryData = DB::table('batteries')
            ->select('batteries.*', 'battery_prices.discount', 'battery_prices.price_net', 'battery_prices.price_retail as price_retail_original', 'battery_prices.discount_price')
            ->join('battery_prices', 'battery_prices.battery_id', '=', 'batteries.id', 'left')
            ->whereIn('batteries.id', $selectedBatteryIds)
            ->get();

        return $batteryData;
    }

    public static function getBatteryWithSize()
    {
        $batteryData = DB::table('batteries')
            ->select('batteries.*', 'battery_size_categories.name as size_category_name')
            ->join('battery_size_categories', 'batteries.size_category_id', '=', 'battery_size_categories.id', 'left')
            ->get()
            ->toArray();

        return $batteryData;
    }

    public static function getBatteryData($batteryId)
    {
        return self::with('brand', 'subbrandCategory', 'usageType', 'sizeCategory', 'technology', 'batteryPrices')
            ->whereIn('id', $batteryId)
            ->get();
    }

    public function batteryPrices(): HasMany
    {
        return $this->hasMany(BatteryPriceModel::class, 'battery_id', 'id');
    }

    public function batteryPricesBelong(): BelongsTo
    {
        return $this->belongsTo(BatteryPriceModel::class, 'id', 'battery_id');
    }

    public function vehicleBatteryBelong()
    {
        return $this->belongsTo(VehicleBatteryModel::class, 'id', 'battery_id')
            ->with('vehicle');
    }

    public function batteryImages(): HasMany
    {
        return $this->hasMany(BatteryImageModel::class, 'battery_id', 'id');
    }
}
