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
    protected $table = 'vehicle';

    protected $fillable = [
        'id',
        'name',
        'id_brand',
        'url',
    ];

    /**
     * Get vehicle brand.
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(VehicleBrandModel::class, 'id_brand');
    }

    /**
     * Get all of the customers who own the vehicle.
     */
    public function customers()
    {
        return $this->belongsToMany(CustomerModel::class, 'customer_vehicle', 'id_vehicle', 'id_customer')
            ->withTimestamps();
    }

    /**
     * Get all of the batteries suitable for the vehicle.
     */
    public function batteries()
    {
        return $this->belongsToMany(BatteryModel::class, 'vehicle_battery', 'id_vehicle', 'id_battery')
            ->withTimestamps();
    }
}
