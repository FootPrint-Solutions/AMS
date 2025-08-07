<?php

namespace App\Models\Orders\SalesInvoice;

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
use App\Models\MasterData\Vehicle\VehicleModel;
use App\Models\Settings\PaymentMethodModel;
use App\Models\Orders\SalesInvoice\SalesInvoiceBatteryModel;
use App\Models\Orders\SalesInvoice\SalesInvoiceExpenseModel;
use App\Models\Orders\SalesOrder\SalesOrderModel;

class SalesInvoiceModel extends Model implements Auditable
{
    use HasFactory, SoftDeletes, DataTablesTrait, AuditableTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sales_invoices';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sales_order_id',
        'sales_invoice_number',
        'invoice_number',
        'date',
        'customer_id',
        'vehicle_id',
        'distributor_shop_id',
        'distributor_shop_technician_id',
        'discount',
        'discount_price',
        'subtotal',
        'total_expenses',
        'total',
        'payment_status',
        'status',
        'address',
        'alternative_address',
        'latitude',
        'longitude',
        'payment_method_id',
        'midtrans_invoice_number',
        'midtrans_payment_link',
        'source_platform',
        'source_id'
    ];

    /**
     * Get the customer of the invoice.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(CustomerModel::class);
    }

    /**
     * Get the distributor shop of the invoice.
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(DistributorShopModel::class, "distributor_shop_id");
    }

    /**
     * Get the distributor shop technician of the invoice.
     */
    public function technician(): BelongsTo
    {
        return $this->belongsTo(DistributorShopTechnicianModel::class, "distributor_shop_technician_id");
    }

    /**
     * Get the vehicle of the invoice.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(VehicleModel::class, "vehicle_id");
    }

    /**
     * Get the payment method of the invoice.
     */
    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethodModel::class, "payment_method_id");
    }

    /**
     * Get the distributor shop has many sales order.
     */
    public function distributorShop(): BelongsTo
    {
        return $this->belongsTo(DistributorShopModel::class, "distributor_shop_id");
    }

    /**
     * Generate new sales invoice code.
     */
    public static function newCode()
    {
        $latestCode = self::withTrashed()
            ->orderByDesc('created_at')
            ->first()?->sales_invoice_number ?? null;

        $year = substr($latestCode, 2, 2);
        $month = substr($latestCode, 4, 2);
        $currentYear = date('y');
        $currentMonth = date('m');

        $newCode = "SI";
        if ($year == $currentYear) {
            if ($month == $currentMonth) {
                $iteration = substr($latestCode, 6);
                $nextIteration = str_pad((int)$iteration + 1, strlen($iteration), '0', STR_PAD_LEFT);
                $newCode .= $year . $month . $nextIteration;
            } else {
                $newCode .= $year . $currentMonth . '00001';
            }
        } else {
            $newCode .= $currentYear . $currentMonth . '00001';
        }
        return $newCode;
    }

    /**
     * Get all data for DataTables.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public static function allForDataTables($request)
    {
        $selectColumns = [
            'sales_invoices.*',
            'customers.name AS customer_name',
            'vehicles.name AS vehicle_name',
            'shops.name AS shop_name',
            'distributors.id AS distributor_id',
            'distributors.name AS distributor_name',
            'technicians.name AS technician_name',
            'payment_methods.name AS payment_method_name',
            'sales_orders.sales_order_number AS sales_order_number',
        ];
        $searchColumns = ['sales_invoice_number', 'sales_order_number', 'invoice_number', 'customers.name', 'shops.name', 'distributors.name', 'technicians.name'];

        $orderColumns = [
            'id',
            'sales_invoice_number',
            'sales_order_number',
            'invoice_number',
            'date',
            'customer_name',
            'vehicle_name',
            'shop_name',
            'distributor_name',
            'technician_name',
            'total',
            'payment_method_name',
            'status'
        ];

        $query = self::query();
        $query->leftJoin("customers", "sales_invoices.customer_id", "=", "customers.id");
        $query->leftJoin("vehicles", "sales_invoices.vehicle_id", "=", "vehicles.id");
        $query->leftJoin("distributor_shops AS shops", "sales_invoices.distributor_shop_id", "=", "shops.id");
        $query->leftJoin("distributors", "shops.distributor_id", "=", "distributors.id");
        $query->leftJoin("distributor_shop_technicians AS technicians", "sales_invoices.distributor_shop_technician_id", "=", "technicians.id");
        $query->leftJoin("payment_methods", "sales_invoices.payment_method_id", "=", "payment_methods.id");
        $query->leftJoin("sales_orders", "sales_invoices.sales_order_id", "=", "sales_orders.id");

        if ($request->dateStart && $request->dateEnd) {
            $query->whereBetween('sales_invoices.date', [$request->dateStart, $request->dateEnd]);
        }

        $query->select($selectColumns);

        return self::getAllRowSalesOrders($request, $query, $selectColumns, $searchColumns, null, $orderColumns);
    }

    public function batteries(): HasMany
    {
        return $this->hasMany(SalesInvoiceBatteryModel::class, 'sales_invoice_id')
            ->with('battery');
    }

    /**
     * Get the expenses associated with the sales invoice.
     */
    public function expenses(): HasMany
    {
        return $this->hasMany(SalesInvoiceExpenseModel::class, 'sales_invoice_id')
            ->with('expense');
    }

    /**
     * Get the sales order associated with the sales invoice.
     */
    public function salesOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrderModel::class, 'sales_order_id');
    }
}
