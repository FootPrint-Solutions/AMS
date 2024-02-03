<?php

namespace App\Models\MasterData\Battery;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BatteryModel extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'battery';

    /**
     * Get battery brand.
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(BatteryBrandModel::class, 'id_brand');
    }

    /**
     * Get battery subbrand category.
     */
    public function subbrandCategory(): BelongsTo
    {
        return $this->belongsTo(BatterySubbrandCategoryModel::class, 'id_subbrand_category');
    }

    /**
     * Get battery usage type.
     */
    public function usageType(): BelongsTo
    {
        return $this->belongsTo(BatteryUsageTypeModel::class, 'id_usage_type');
    }

    /**
     * Get battery size category.
     */
    public function sizeCategory(): BelongsTo
    {
        return $this->belongsTo(BatterySizeCategoryModel::class, 'id_size_category');
    }

    /**
     * Get battery technology.
     */
    public function technology(): BelongsTo
    {
        return $this->belongsTo(BatteryTechnologyModel::class, 'id_technology');
    }

    /**
     * Get all of the battery aliases.
     */
    public function aliases(): HasMany
    {
        return $this->hasMany(BatteryAlias::class, "id_battery")
            ->withTimestamps();
    }
}
