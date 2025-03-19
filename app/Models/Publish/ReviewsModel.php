<?php

namespace App\Models\Publish;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use App\Models\MasterData\Vehicle\VehicleModel as Vehicle;

// TRAITS
use App\Traits\DataTablesTrait;
use OwenIt\Auditing\Auditable as AuditableTrait;


class ReviewsModel extends Model implements Auditable
{
    use HasFactory, SoftDeletes, DataTablesTrait, AuditableTrait;


    protected $table = 'reviews';

    protected $fillable = [
        'name',
        'vehicle_id',
        'testimonial',
        'stars',
        'user_photo',
        'testimonial_photo',
    ];

    /**
     * Define the relationship with the Vehicle model.
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Get all data for DataTables.
     * 
     * @param \Illuminate\Http\Request $request The POST request obtained (for DataTables configuration).
     * @return array Associative array containing data for DataTables display.
     */
    public static function allForDataTables($request)
    {
        // Set the list of select and search columns.
        $selectColumns = ['id', 'name', 'vehicle_id', 'testimonial', 'stars', 'user_photo', 'testimonial_photo'];
        $searchColumns = ['name', 'vehicle_id', 'testimonial', 'stars'];

        // Build the query to obtain all rows.
        $query = self::query();
        $query->select($selectColumns);

        return self::getAllRows($request, $query, $selectColumns, $searchColumns);
    }
}
