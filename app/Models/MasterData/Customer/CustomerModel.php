<?php

namespace App\Models\MasterData\Customer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

// Models
use App\Models\MasterData\Vehicle\VehicleModel;

class CustomerModel extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'customers';

    protected $fillable = [
        'id', 'name', 'address', 'contact', 'email',
        // Add other fillable columns here if any
    ];

    /**
     * Get all of the vehicles owned by the customer.
     */
    public function vehicles()
    {
        return $this->belongsToMany(VehicleModel::class, 'customer_vehicle', 'customer_id', 'vehicle_id')
            ->withTimestamps();
    }

    public static function getFillableColumns()
    {
        return (new static())->getFillable();
    }
}
