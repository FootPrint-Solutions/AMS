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

    protected $fillable = [
        'id',
        'name',
        'brand_id',
        'url',
    ];

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
}
