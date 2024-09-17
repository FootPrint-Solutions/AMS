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
use App\Models\Settings\PaymentMethodModel;

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
        return $this->hasMany(WorkOrderBatteryModel::class, 'work_order_id')
            ->limit(3);
    }

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrderModel::class, 'sales_order_id')
            ->with('distributorShop', 'vehicle')
            ->with('paymentMethod')
            ->with('batteries');
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

    public function paymentMethod()
    {
        // from sales order join with payment method 
        return $this->belongsTo(PaymentMethodModel::class, 'payment_method_id');
    }


    public static function allForDataTables($request)
    {
        $search = $request->input("search.value");
        $order = $request->input("order.0.column");
        $dir = $request->input("order.0.dir");
        $start = $request->input("start");
        $length = $request->input("length");
        $start_date = $request->input("dateStart");
        $end_date = $request->input("dateEnd");

        // Get data from the work_orders table for the datatable
        $query = self::select('work_orders.*', 'sales_orders.sales_order_number', 'customers.name as customer_name')
            ->leftJoin('sales_orders', 'work_orders.sales_order_id', '=', 'sales_orders.id')
            ->leftJoin('customers', 'work_orders.customer_id', '=', 'customers.id')
            ->addSelect([
                'qty' => WorkOrderBatteryModel::selectRaw('sum(quantity)')
                    ->whereColumn('work_order_id', 'work_orders.id')
            ])
            ->where(function ($query) use ($search) {
                $query->where('work_orders.work_order_number', 'like', "%$search%")
                    ->orWhere('sales_orders.sales_order_number', 'like', "%$search%")
                    ->orWhere('customers.name', 'like', "%$search%");
            })
            ->orderBy('work_orders.id', 'desc')
            ->offset($start)
            ->limit($length);

        if (!empty($start_date) && !empty($end_date)) {
            $query->whereBetween('work_orders.date', [$start_date, $end_date]);
        }

        $data = $query->get();

        $countQuery = self::select('work_orders.*', 'sales_orders.sales_order_number', 'customers.name as customer_name')
            ->leftJoin('sales_orders', 'work_orders.sales_order_id', '=', 'sales_orders.id')
            ->leftJoin('customers', 'work_orders.customer_id', '=', 'customers.id')
            ->where(function ($query) use ($search) {
                $query->where('work_orders.work_order_number', 'like', "%$search%")
                    ->orWhere('sales_orders.sales_order_number', 'like', "%$search%")
                    ->orWhere('customers.name', 'like', "%$search%");
            });

        if (!empty($start_date) && !empty($end_date)) {
            $countQuery->whereBetween('work_orders.date', [$start_date, $end_date]);
        }

        $count = $countQuery->count();

        return [
            'row' => $data,
            'count' => $count
        ];
    }

    public static function getWorkOrderData($id)
    {
        return self::with('batteries', 'salesOrder.batteries', 'customer', 'distributorShop', 'paymentMethod')
            ->where('id', $id)
            ->first();
    }

    public static function updateImagePath($id, $path)
    {
        return self::where('id', $id)
            ->update([
                'image' => $path
            ]);
    }

    public static function updateFileCompleteWorkOrderPath($id, $path)
    {
        return self::where('id', $id)
            ->update([
                'attachment_file' => $path
            ]);
    }

    public static function updateStatusCompletedWorkOrderSalesOrder($id)
    {
        // update status work order to completed
        $workOrder = self::find($id);
        $workOrder->status = 'completed';
        $workOrder->save();

        // update status sales order to completed
        $salesOrder = SalesOrderModel::find($workOrder->sales_order_id);
        $salesOrder->status = 'completed';
        $salesOrder->save();
    }

    public static function lazyLoadList($request)
    {
        $search = $request->input("search");
        $order = $request->input("order.0.column");
        $dir = $request->input("order.0.dir");
        $start = $request->input("offset");
        $length = $request->input("limit");

        // get data from table work order for datatable
        $data = self::select('work_orders.*', 'sales_orders.sales_order_number', 'customers.name as customer_name', 'sales_orders.status')
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
}
