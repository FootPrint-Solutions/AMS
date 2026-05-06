<?php

namespace App\Models\MasterData\Battery;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BatteryBackupModel extends Model
{
    use HasFactory;

    protected $table = 'battery_backups';

    protected $fillable = [
        'backup_number',
        'backup_date',
        'battery_id',
        'code',
        'name',
        'name_alternate',
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
        'image',
        'status',
        'type',
        'editable_price',
    ];

    protected $casts = [
        'backup_date' => 'datetime',
        'brand_id' => 'integer',
        'subbrand_category_id' => 'integer',
        'usage_type_id' => 'integer',
        'size_category_id' => 'integer',
        'technology_id' => 'integer',
        'dimension_length' => 'float',
        'dimension_width' => 'float',
        'dimension_height' => 'float',
        'standard_cca' => 'float',
        'capacity' => 'float',
        'warranty' => 'integer',
        'price_retail' => 'float',
        'price_buy' => 'float',
        'status' => 'boolean',
        'editable_price' => 'boolean',
    ];

    public function brand()
    {
        return $this->belongsTo(BatteryBrandModel::class, 'brand_id');
    }

    public function subbrandCategory()
    {
        return $this->belongsTo(BatterySubbrandCategoryModel::class, 'subbrand_category_id');
    }

    public function usageType()
    {
        return $this->belongsTo(BatteryUsageTypeModel::class, 'usage_type_id');
    }

    public function sizeCategory()
    {
        return $this->belongsTo(BatterySizeCategoryModel::class, 'size_category_id');
    }

    public function technology()
    {
        return $this->belongsTo(BatteryTechnologyModel::class, 'technology_id');
    }
}
