<?php

namespace App\Models\MasterData\Battery;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// TRAITS
use App\Traits\DataTablesTrait;

class BatteryModel extends Model
{
    use HasFactory, SoftDeletes, DataTablesTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'batteries';

    /**
     * The list of columns in the associated table.
     */
    private static $selectColumns = [
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
     * @param int $start The starting index of rows.
     * @param int $length The number of rows to be returned.
     * @param string $searchValue The search filter value.
     * @param int $orderColumn The column index for ordering.
     * @param int $orderDirection Ascending or descending order.
     * @return array Associative array containing data for DataTables display.
     */
    public static function allForDataTables($start, $length, $searchValue, $orderColumn, $orderDirection)
    {
        return self::getAllRows($start, $length, $searchValue, $orderColumn, $orderDirection, self::$selectColumns);
    }
}
