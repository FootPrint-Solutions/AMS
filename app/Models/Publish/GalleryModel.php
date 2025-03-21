<?php

namespace App\Models\Publish;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

// TRAITS
use App\Traits\DataTablesTrait;
use OwenIt\Auditing\Auditable as AuditableTrait;

use App\Models\MasterData\Vehicle\VehicleModel;
use App\Models\MasterData\Battery\BatteryModel;


class GalleryModel extends Model implements Auditable
{
    use HasFactory, SoftDeletes, DataTablesTrait, AuditableTrait;

    protected $table = 'galleries';

    protected $fillable = [
        'battery_id',
        'vehicle_id',
        'photo',
        'status',
    ];

    /**
     * Get all data for DataTables.
     * 
     * @param \Illuminate\Http\Request $request The POST request obtained (for DataTables configuration).
     * @return array Associative array containing data for DataTables display.
     */
    public static function allForDataTables($request)
    {
        // Set the list of select and search columns.
        $selectColumns = ['galleries.id', 'batteries.name as battery_name', 'vehicles.name as vehicle_name', 'galleries.photo', 'galleries.status'];
        $searchColumns = ['galleries.photo', 'batteries.name', 'vehicles.name'];

        // Build the query to obtain all rows with joins.
        $query = self::query();
        $query->select($selectColumns)
            ->join('batteries', 'galleries.battery_id', '=', 'batteries.id')
            ->join('vehicles', 'galleries.vehicle_id', '=', 'vehicles.id');

        return self::getAllRowsGallery($request, $query, $selectColumns, $searchColumns);
    }

    /**
     * Get the battery associated with the gallery.
     */
    public function battery()
    {
        return $this->belongsTo(BatteryModel::class, 'battery_id', 'id');
    }

    /**
     * Get the vehicle associated with the gallery.
     */
    public function vehicle()
    {
        return $this->belongsTo(VehicleModel::class, 'vehicle_id', 'id');
    }
}
