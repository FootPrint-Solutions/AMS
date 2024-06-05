<?php

namespace App\Models\Orders\WorkOrder;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

// TRAITS
use App\Traits\DataTablesTrait;
use OwenIt\Auditing\Auditable as AuditableTrait;

// MODEL
use App\Models\Orders\SalesOrder\SalesOrderModel;
use App\Models\MasterData\Customer\CustomerModel;
use App\Models\MasterData\Distributor\DistributorShopModel;

class WorkOrderModel extends Model implements Auditable
{
    use HasFactory, SoftDeletes, DataTablesTrait, AuditableTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'work_orders';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'work_order_number',
        'date',
        'sales_order_id',
        'customer_id',
        'tax',
        'tax_price',
        'discount_price',
        'discount',
        'total',
        'status',
        'address',
        'latitude',
        'longitude'
    ];

    public static function newCode()
    {
        // Get the latest added code.
        $latestCode = self::withTrashed()
            ->orderByDesc('created_at')
            ->first()?->work_order_number ?? null;

        // Generate the new sales order code.
        $year = substr($latestCode, 2, 2);
        $month = substr($latestCode, 4, 2);
        $currentYear = date('y');
        $currentMonth = date('m');

        $newCode = "WO";
        if ($year == $currentYear) {
            if ($month == $currentMonth) {
                // Generate new code with new iteration only.
                $iteration = substr($latestCode, 6);
                $nextIteration = str_pad((int)$iteration + 1, strlen($iteration), '0', STR_PAD_LEFT);
                $newCode .= $year . $month . $nextIteration;
            } else {
                // Generate new code with new month.
                $newCode .= $year . $currentMonth . '00001';
            }
        } else {
            // Generate new code with new year and new month.
            $newCode .= $currentYear . $currentMonth . '00001';
        }
        return $newCode;
    }

    public function batteries()
    {
        return $this->hasMany(WorkOrderBatteryModel::class, 'work_order_id');
    }

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrderModel::class, 'sales_order_id')
            ->with('distributorShop', 'vehicle');
    }

    public function customer()
    {
        return $this->belongsTo(CustomerModel::class, 'customer_id');
    }

    // distributor shop from sales order 
    public function distributorShop()
    {
        return $this->belongsTo(DistributorShopModel::class, 'distributor_shop_id');
    }


    public static function allForDataTables($request)
    {
        $search = $request->input("search.value");
        $order = $request->input("order.0.column");
        $dir = $request->input("order.0.dir");
        $start = $request->input("start");
        $length = $request->input("length");

        // get data from table work order for datatable
        $data = self::select('work_orders.*', 'sales_orders.sales_order_number', 'customers.name as customer_name')
            ->leftJoin('sales_orders', 'work_orders.sales_order_id', '=', 'sales_orders.id')
            ->leftJoin('customers', 'work_orders.customer_id', '=', 'customers.id')
            ->addSelect([
                'qty' => WorkOrderBatteryModel::selectRaw('sum(quantity)')
                    ->whereColumn('work_order_id', 'work_orders.id')
            ])
            ->where('work_orders.work_order_number', 'like', "%$search%")
            ->orWhere('sales_orders.sales_order_number', 'like', "%$search%")
            ->orWhere('customers.name', 'like', "%$search%")
            ->orderBy('work_orders.id', 'desc')
            ->offset($start)
            ->limit($length)
            ->get();

        $count = self::select('work_orders.*', 'sales_orders.sales_order_number', 'customers.name as customer_name')
            ->leftJoin('sales_orders', 'work_orders.sales_order_id', '=', 'sales_orders.id')
            ->leftJoin('customers', 'work_orders.customer_id', '=', 'customers.id')
            ->where('work_orders.work_order_number', 'like', "%$search%")
            ->orWhere('sales_orders.sales_order_number', 'like', "%$search%")
            ->orWhere('customers.name', 'like', "%$search%")
            ->count();

        return [
            'row' => $data,
            'count' => $count
        ];
    }

    public static function getWorkOrderData($id)
    {
        return self::with('batteries', 'salesOrder', 'customer', 'distributorShop')
            ->where('id', $id)
            ->first();
    }
}
