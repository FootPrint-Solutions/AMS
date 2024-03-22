<?php

namespace App\Models\MasterData\Customer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

// Models
use App\Models\MasterData\Vehicle\VehicleModel;

// Trait
use App\Traits\DataTablesTrait;

class CustomerModel extends Model
{
    use HasFactory, SoftDeletes, DataTablesTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'customers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'address', 'contact', 'email', 'latitude', 'longitude'
    ];

    /**
     * The list of columns in the associated table.
     */
    private static $selectColumns = [
        'id', 'name', 'address', 'contact', 'email'
    ];

    /**
     * Get all of the vehicles owned by the customer.
     */
    public function vehicles()
    {
        return $this->belongsToMany(VehicleModel::class, 'customer_vehicle', 'customer_id', 'vehicle_id')
            ->withTimestamps();
    }

    /**
     * Get all data for DataTables.
     * 
     * @param \Illuminate\Http\Request $request The POST request obtained (for DataTables configuration).
     * @return array Associative array containing data for DataTables display.
     */
    public static function allForDataTables($request)
    {
        // Build the query to obtain all rows.
        $query = self::query();
        $query->select(self::$selectColumns);

        return self::getAllRows($request, $query, self::$selectColumns);
    }

    public static function quotations()
    {
        return self::hasMany(QuotationModel::class, 'customer_id', 'id');
    }
}
