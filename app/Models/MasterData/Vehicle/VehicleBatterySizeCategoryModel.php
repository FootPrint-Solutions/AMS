<?php

namespace App\Models\MasterData\Vehicle;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

// TRAITS
use OwenIt\Auditing\Auditable as AuditableTrait;

class VehicleBatterySizeCategoryModel extends Model implements Auditable
{
    use HasFactory, AuditableTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vehicle_battery_size_category';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['vehicle_id', 'battery_size_category_id'];
}
