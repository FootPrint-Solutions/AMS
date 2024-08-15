<?php

namespace App\Models\Orders\WorkOrder;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// TRAITS
use App\Traits\DataTablesTrait;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class TrackingModel extends Model implements Auditable
{
    use HasFactory, DataTablesTrait, AuditableTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'trackings';

    // relationship with work order
    public function workOrder()
    {
        return $this->belongsTo(WorkOrderModel::class, 'work_order_id', 'id')
            ->with('salesOrder')
            ->with('salesOrder.customer');
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
        $selectColumns = ['id', 'work_order_id', 'latitude_start', 'longitude_start', 'longitude_end', 'latitude_end', 'latitude_current', 'longitude_current'];
        $searchColumns = ['work_order_id', 'latitude_start', 'longitude_start', 'longitude_end', 'latitude_end', 'latitude_current', 'longitude_current'];

        // Build the query to obtain all rows.
        $query = self::query()
            ->with('workOrder');
        $query->select($selectColumns);

        return self::getAllRows($request, $query, $selectColumns, $searchColumns);
    }
}
