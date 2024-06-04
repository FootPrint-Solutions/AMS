<?php

namespace App\Models\Orders\SalesOrder;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

// TRAITS
use App\Traits\DataTablesTrait;
use OwenIt\Auditing\Auditable as AuditableTrait;

// CONTROLLER
use App\Models\MasterData\Customer\CustomerModel;
use App\Models\MasterData\Distributor\DistributorShopModel;
use App\Models\MasterData\Distributor\DistributorShopTechnicianModel;
use App\Models\Orders\WorkOrder\WorkOrderModel;
use App\Models\Orders\WorkOrder\WorkOrderBatteryModel;

class SalesOrderModel extends Model implements Auditable
{
    use HasFactory, SoftDeletes, DataTablesTrait, AuditableTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sales_orders';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sales_order_number',
        'date',
        'customer_id',
        'distributor_shop_id',
        'distributor_shop_technician_id',
        'tax',
        'tax_price',
        'discount_price',
        'discount',
        'extra_discount',
        'subtotal',
        'total',
        'status',
        'address',
        'latitude',
        'longitude',
        'status',
        'payment_method_id',
        'midtrans_invoice_number',
        'midtrans_payment_link'
    ];

    /**
     * Get the customer of the quotations.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(CustomerModel::class);
    }

    /**
     * Get the distributor shop of the quotations.
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(DistributorShopModel::class, "distributor_shop_id");
    }

    /**
     * Get the distributor shop technician of the quotations.
     */
    public function technician(): BelongsTo
    {
        return $this->belongsTo(DistributorShopTechnicianModel::class, "distributor_shop_technician_id");
    }

    /**
     * Get all of the batteries of the quotations.
     */
    public function batteries(): HasMany
    {
        return $this->hasMany(SalesOrderBatteryModel::class, "sales_order_id");
    }

    /**
     * 
     */
    public static function newCode()
    {
        // Get the latest added code.
        $latestCode = self::withTrashed()
            ->orderByDesc('created_at')
            ->first()?->sales_order_number ?? null;

        // Generate the new sales order code.
        $year = substr($latestCode, 2, 2);
        $month = substr($latestCode, 4, 2);
        $currentYear = date('y');
        $currentMonth = date('m');

        $newCode = "AK";
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

    /**
     * Get all data for DataTables.
     * 
     * @param \Illuminate\Http\Request $request The POST request obtained (for DataTables configuration).
     * @return array Associative array containing data for DataTables display.
     */
    public static function allForDataTables($request)
    {
        // Set the list of select and search columns.
        $selectColumns = [
            'sales_orders.*',
            'customers.name AS customer_name',
            'shops.name AS shop_name',
            'distributors.id AS distributor_id',
            'distributors.name AS distributor_name',
            'technicians.name AS technician_name',
            'payment_methods.name AS payment_method_name'
        ];
        $searchColumns = ['sales_order_number', 'customers.name', 'shops.name', 'distributors.name', 'technicians.name'];

        // Build the query to obtain all rows.
        $query = self::query();
        $query->leftJoin("customers", "sales_orders.customer_id", "=", "customers.id");
        $query->leftJoin("distributor_shops AS shops", "sales_orders.distributor_shop_id", "=", "shops.id");
        $query->leftJoin("distributors", "shops.distributor_id", "=", "distributors.id");
        $query->leftJoin("distributor_shop_technicians AS technicians", "sales_orders.distributor_shop_technician_id", "=", "technicians.id");
        $query->leftJoin("payment_methods", "sales_orders.payment_method_id", "=", "payment_methods.id");
        $query->select($selectColumns);

        return self::getAllRows($request, $query, $selectColumns, $searchColumns);
    }

    public static function CreateWorkOrder($id)
    {
        $salesOrder = self::with('customer', 'shop', 'technician', 'batteries')->find($id);
        $workOrder = new WorkOrderModel();
        $workOrder->work_order_number = WorkOrderModel::newCode();
        $workOrder->date = $salesOrder->date;
        $workOrder->sales_order_id = $salesOrder->id;
        $workOrder->customer_id = $salesOrder->customer_id;
        $workOrder->tax = $salesOrder->tax;
        $workOrder->tax_price = $salesOrder->tax_price;
        $workOrder->discount_price = $salesOrder->discount_price;
        $workOrder->discount = $salesOrder->discount;
        $workOrder->total = $salesOrder->total;
        $workOrder->address = $salesOrder->address;
        $workOrder->latitude = $salesOrder->latitude;
        $workOrder->longitude = $salesOrder->longitude;
        $workOrder->save();


        $batteries = [];
        foreach ($salesOrder->batteries as $battery) {
            if ($battery->price_net != 0) {
                $BatteryPrice = $battery->price_net;
            } else {
                $BatteryPrice = $battery->battery_price_retail;
            }
            $batteries[] = [
                'battery_id' => $battery->battery_id,
                'battery_name' => $battery->battery_name,
                'battery_price' => $BatteryPrice,
                'quantity' => $battery->quantity,
            ];
        }
        $workOrder->batteries()->createMany($batteries);

        return $workOrder;
    }

    public function workOrder()
    {
        return $this->hasOne(WorkOrderModel::class, 'sales_order_id');
    }
}
