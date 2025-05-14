<?php

namespace App\Models\Orders\SalesOnline;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

use App\Traits\DataTablesTrait;
use OwenIt\Auditing\Auditable as AuditableTrait;

class SalesOnlineModel extends Model implements Auditable
{
    use HasFactory, SoftDeletes, DataTablesTrait, AuditableTrait;

    protected $table = 'sales_online';

    protected $fillable = [
        'customer_name',
        'province',
        'city',
        'district',
        'sub_district',
        'postal_code',
        'phone_number',
        'email',
        'vehicle_plate',
        'delivery_date',
        'additional_info',
        'address',
    ];

    protected $dates = [
        'delivery_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Get all data for DataTables.
     * 
     * @param \Illuminate\Http\Request $request The POST request obtained (for DataTables configuration).
     * @return array Associative array containing data for DataTables display.
     */
    public static function allForDataTables($request)
    {
        $query = self::query();

        // Apply filters based on request parameters
        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('customer_name', 'like', '%' . $request->search . '%')
                    ->orWhere('province', 'like', '%' . $request->search . '%')
                    ->orWhere('city', 'like', '%' . $request->search . '%')
                    ->orWhere('district', 'like', '%' . $request->search . '%')
                    ->orWhere('sub_district', 'like', '%' . $request->search . '%')
                    ->orWhere('postal_code', 'like', '%' . $request->search . '%')
                    ->orWhere('phone_number', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%')
                    ->orWhere('vehicle_plate', 'like', '%' . $request->search . '%');
            });
        }

        return self::dataTables($query, $request);
    }
}
