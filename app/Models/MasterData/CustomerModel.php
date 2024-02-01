<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class CustomerModel extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'customer';

    /**
     * Get all of the vehicles owned by the customer.
     */
    public function vehicles()
    {
        return $this->belongsToMany(VehicleModel::class, 'customer_vehicle', 'id_customer', 'id_vehicle')
            ->withTimestamps();
    }
}
