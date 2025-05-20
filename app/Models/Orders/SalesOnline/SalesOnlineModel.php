<?php

namespace App\Models\Orders\SalesOnline;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

use App\Traits\DataTablesTrait;
use OwenIt\Auditing\Auditable as AuditableTrait;

use App\Models\Orders\SalesOnline\SalesOnlineBatteriesModel;

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
        'whatsapp_status',
    ];

    protected $dates = [
        'delivery_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function batteries()
    {
        return $this->hasMany(SalesOnlineBatteriesModel::class, 'sales_online_id', 'id');
    }

    /**
     * Get all data for DataTables.
     * 
     * @param \Illuminate\Http\Request $request The POST request obtained (for DataTables configuration).
     * @return array Associative array containing data for DataTables display.
     */
    public static function allForDataTables($request)
    {
        $start = $request->input("start");
        $length = $request->input("length");
        $searchValue = $request->input("search.value");
        $orderColumn = $request->input("order.0.column");
        $orderDirection = $request->input("order.0.dir");

        $selectColumns = [
            'id',
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
            'sum_total' => SalesOnlineBatteriesModel::selectRaw('SUM(price * quantity)')->whereColumn('sales_online_batteries.sales_online_id', 'sales_online.id'),
            'sum_quantity' => SalesOnlineBatteriesModel::selectRaw('SUM(quantity)')->whereColumn('sales_online_batteries.sales_online_id', 'sales_online.id'),
            'sales_order_id' => \DB::table('sales_orders')
                ->select('id')
                ->whereColumn('source_id', 'sales_online.id')
                ->limit(1),
            'whatsapp_status',
        ];

        $searchColumns = [
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
            'address'
        ];

        $orderDefault = [
            "column" => "updated_at",
            "direction" => "desc"
        ];

        $query = self::query();
        $query->select($selectColumns);

        if ($searchColumns != null && $searchValue != null) {
            $query->where(function ($query) use ($searchValue, $searchColumns) {
                foreach ($searchColumns as $column) {
                    $query->orWhere($column, "LIKE", "%" . $searchValue . "%");
                }
            });
        }

        if ($orderColumn !== null) {
            $columnName = $selectColumns[$orderColumn] ?? null;
            if ($columnName !== null) {
                $query->orderBy($columnName, $orderDirection);
            }
        } else {
            if ($orderDefault !== null) {
                $query->orderBy($orderDefault["column"], $orderDefault["direction"]);
            } else {
                $query->orderBy("updated_at", "desc");
            }
        }

        return array(
            "count" => $query->count(),
            "row" => $query->skip($start)
                ->take($length)
                ->get(),
        );
    }
}
