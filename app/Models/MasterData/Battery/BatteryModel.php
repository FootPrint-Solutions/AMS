<?php

namespace App\Models\MasterData\Battery;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Contracts\Auditable;

// TRAITS
use App\Traits\DataTablesTrait;
use OwenIt\Auditing\Auditable as AuditableTrait;

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
        'name_alternate'
    ];

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
            'name_alternate'
        ];
        $searchColumns = [
            'name',
            'name_alternate'
        ];

        // Build the query to obtain all rows.
        $query = self::query();
        $query->select($selectColumns);

        return self::getAllRows($request, $query, $selectColumns, $searchColumns);
    }

    /**
     * Get rows for autocomplete.
     * 
     * @param string $keyword The autocomplete keyword.
     * @param array $extraColumn The list of other columns except id and name to obtain.
     * @param int $limit The limit number of rows returned.
     */
    public static function allForAutocomplete($keyword, $extraColumn, $limit = 5)
    {
        $columns = ["id", "name"];
        $query = self::select(array_merge($columns, $extraColumn))
            ->where("name", "like", "%{$keyword}%")
            ->take($limit)
            ->get()
            ->toArray();

        return $query;
    }

    public static function getBatteryDistributor($selectedBatteryIds, $distributorShopId)
    {
        $batteryData = DB::table('batteries')
            ->select('batteries.id', 'batteries.name', 'distributor_shop_battery.distributor_shop_id', 'distributor_shop_battery.price as price_retail', 'distributor_shop_battery.url')
            ->join('distributor_shop_battery', 'batteries.id', '=', 'distributor_shop_battery.battery_id', 'left')
            ->whereIn('batteries.id', $selectedBatteryIds)
            ->where('distributor_shop_battery.distributor_shop_id', $distributorShopId)
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
}
