<?php

namespace App\Models\MasterData\Battery;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class BatteryUsageTypeModel extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'battery_usage_type';

    protected $fillable = [
        'name',
    ];
}
