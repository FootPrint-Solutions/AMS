<?php

namespace App\Models\Orders\SalesOrder;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Support\Facades\DB;

// TRAITS
use App\Traits\DataTablesTrait;
use OwenIt\Auditing\Auditable as AuditableTrait;

// Models
use App\Models\MasterData\Customer\CustomerModel;
use App\Models\MasterData\Distributor\DistributorModel;
use App\Models\MasterData\Distributor\DistributorShopModel;
use App\Models\MasterData\Distributor\DistributorShopTechnicianModel;
use App\Models\Orders\WorkOrder\WorkOrderModel;
use App\Models\Orders\WorkOrder\WorkOrderBatteryModel;
use App\Models\MasterData\Vehicle\VehicleModel;
use App\Models\Settings\PaymentMethodModel;
use App\Models\Inventory\InventoryModel;
use App\Models\Inventory\InventoryDetailModel;
use App\Models\Inventory\InventoryRecycleModel;
use App\Models\Inventory\InventoryRecycleDetailModel;
use App\Models\Accounting\BillingInvoiceExpenseModel;
use Illuminate\Support\Facades\Log as Logger;

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
        'invoice_number',
        'date',
        'customer_id',
        'vehicle_id',
        'distributor_shop_id',
        'distributor_shop_technician_id',
        'discount',
        'discount_price',
        'subtotal',
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
        'source_id',
        'vendor',
        'ship_to',
        'type',
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
        return $this->hasMany(SalesOrderBatteryModel::class, "sales_order_id")
            ->with('battery');
    }

    /**
     * Get all of the batteries of the quotations.
     */
    public function details(): HasMany
    {
        return $this->hasMany(SalesOrderBatteryModel::class, "sales_order_id")
            ->with('battery');
    }

    /**
     * Get the distributor shop has many sales order.
     */
    public function distributorShop(): BelongsTo
    {
        return $this->belongsTo(DistributorShopModel::class, "distributor_shop_id");
    }

    /**
     * Get the vehicle has many sales order.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(VehicleModel::class, "vehicle_id");
    }

    /**
     * Get the payment method of the quotations.
     */
    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethodModel::class, "payment_method_id");
    }

    /**
     * Get the vendor distributor shop of the sales order.
     */
    public function vendorData(): BelongsTo
    {
        return $this->belongsTo(DistributorShopModel::class, "vendor");
    }

    /**
     * Get the ship to distributor of the sales order.
     */
    public function shipToData(): BelongsTo
    {
        return $this->belongsTo(DistributorModel::class, "ship_to");
    }


    /**
     * 
     */
    public static function newCode()
    {
        // Get the latest added code.
        $latestCode = self::withTrashed()
            ->where('sales_order_number', 'like', 'AK%')
            ->orderByDesc('sales_order_number')
            ->value('sales_order_number');

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
            'sales_orders.id',
            'sales_orders.sales_order_number',
            'sales_orders.invoice_number',
            'sales_orders.date',
            'sales_orders.total',
            'sales_orders.status',
            'sales_orders.payment_status',
            'sales_orders.type',
            'customers.name AS customer_name',
            'vehicles.name AS vehicle_name',
            'shops.name AS shop_name',
            'distributors.id AS distributor_id',
            'distributors.name AS distributor_name',
            'technicians.name AS technician_name',
            'payment_methods.name AS payment_method_name',
            'billing.billing_number AS billing_number',
            'vendor.name AS vendor_name',
            'ship_to.name AS ship_to_name',
        ];
        $searchColumns = ['sales_order_number', 'sales_orders.invoice_number', 'customers.name', 'shops.name', 'distributors.name', 'technicians.name'];

        $orderColumns = [
            'id',
            'id',
            'sales_order_number',
            'invoice_number',
            'date',
            'customer_name',
            'vehicle_name',
            'distributor_name',
            'technician_name',
            'total',
            'payment_method_name',
            'status'
        ];

        // Build the query to obtain all rows.
        $query = self::query();
        $query->leftJoin('customers', 'sales_orders.customer_id', '=', 'customers.id');
        $query->leftJoin('vehicles', 'sales_orders.vehicle_id', '=', 'vehicles.id');
        $query->leftJoin('distributor_shops AS shops', 'sales_orders.distributor_shop_id', '=', 'shops.id');
        $query->leftJoin('distributors', 'shops.distributor_id', '=', 'distributors.id');
        $query->leftJoin('distributor_shop_technicians AS technicians', 'sales_orders.distributor_shop_technician_id', '=', 'technicians.id');
        $query->leftJoin('payment_methods', 'sales_orders.payment_method_id', '=', 'payment_methods.id');
        $query->leftJoin('billing_invoices AS billing_invoices', 'sales_orders.id', '=', 'billing_invoices.invoice_id');
        $query->leftJoin('billings AS billing', 'billing_invoices.billing_id', '=', 'billing.id');
        $query->leftJoin('distributor_shops AS vendor', 'sales_orders.vendor', '=', 'vendor.id');
        $query->leftJoin('distributors AS ship_to', 'sales_orders.ship_to', '=', 'ship_to.id');

        // filter tanggal
        if ($request->dateStart && $request->dateEnd) {
            $query->whereBetween('sales_orders.date', [$request->dateStart, $request->dateEnd]);
        }

        // filter sales order type
        if ($request->salesOrderType) {
            $query->where('sales_orders.type', $request->salesOrderType);
        }

        $query->select($selectColumns);

        // order by
        if ($request->order) {
            $orderColumnIndex = $request->input("order.0.column");
            $orderDirection = $request->input("order.0.dir");
            $orderColumnName = $orderColumns[$orderColumnIndex] ?? null;
            if ($orderColumnName !== null) {
                $query->orderBy($orderColumnName, $orderDirection);
            }
        } else {
            $query->orderBy("sales_orders.date", "desc");
        }



        return self::getAllRowSalesOrders($request, $query, $selectColumns, $searchColumns, null, $orderColumns);
    }

    /**
     * Create Work Order from Sales Order
     */
    public static function CreateWorkOrder($id)
    {
        $salesOrder = self::with('customer', 'shop', 'technician', 'batteries')->find($id);
        $workOrder = new WorkOrderModel();
        $workOrder->work_order_number = WorkOrderModel::newCode();
        $workOrder->date = $salesOrder->date;
        $workOrder->sales_order_id = $salesOrder->id;
        $workOrder->customer_id = $salesOrder->customer_id;
        $workOrder->discount_price = $salesOrder->discount_price;
        $workOrder->discount = $salesOrder->discount;
        $workOrder->total = $salesOrder->total;
        $workOrder->address = $salesOrder->address;
        $workOrder->latitude = $salesOrder->latitude;
        $workOrder->longitude = $salesOrder->longitude;
        $status = $workOrder->save();


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

        return $status;
    }

    public function workOrder()
    {
        return $this->hasOne(WorkOrderModel::class, 'sales_order_id');
    }

    /**
     * Get sales order summary based on date range and type.
     *
     * @param string $dateStart The start date for the summary.
     * @param string $dateEnd The end date for the summary.
     * @param string|null $salesOrderType The type of sales order (optional).
     * @return array Associative array containing total quantity, total transactions, and total nominal.
     */
    public static function getSalesOrderSummary($dateStart, $dateEnd, $salesOrderType)
    {
        $query = self::query();

        if ($dateStart && $dateEnd) {
            $query->whereBetween('sales_orders.date', [$dateStart, $dateEnd]);
        }

        if ($salesOrderType) {
            $query->where('sales_orders.type', $salesOrderType);
        }

        $totalQtyQuery = DB::table('sales_order_battery')
            ->join('sales_orders', 'sales_order_battery.sales_order_id', '=', 'sales_orders.id');

        if ($dateStart && $dateEnd) {
            $totalQtyQuery->whereBetween('sales_orders.date', [$dateStart, $dateEnd]);
        }

        if ($salesOrderType) {
            $totalQtyQuery->where('sales_orders.type', $salesOrderType);
        }

        $totalQty = (int) $totalQtyQuery->sum('sales_order_battery.quantity');
        $totalTransactions = $query->count();
        $totalNominal = $query->sum('sales_orders.total');

        return [
            'total_qty' => $totalQty,
            'total_transactions' => $totalTransactions,
            'total_nominal' => $totalNominal,
        ];
    }

    public static function sendToInventorySystem($salesOrderIds)
    {
        $salesOrders = self::with('batteries')->whereIn('id', $salesOrderIds)->get();

        $status = true;
        $logMessage = '';
        $logMessage .= "Function sendToInventorySystem: Starting inventory update for Sales Order IDs: " . implode(', ', $salesOrderIds) . "\n";
        foreach ($salesOrders as $salesOrder) {
            $logMessage .= "Function sendToInventorySystem: Processing Sales Order ID " . $salesOrder->id . " with Sales Order Number " . $salesOrder->sales_order_number . "\n";
            $salesOrderBattery = $salesOrder->batteries;
            foreach ($salesOrderBattery as $battery) {
                $logMessage .= "Function sendToInventorySystem: Processing Battery ID " . $battery->battery_id . " of type " . $battery->type . " with quantity " . $battery->quantity . "\n";

                $checkType = $battery->type;
                $qty = $battery->quantity;
                if ($checkType == 'recycle') {
                    $inventory = InventoryRecycleModel::firstOrNew([
                        'battery_recycle_id' => $battery->battery_id
                    ]);

                    $inventory->stock = ($inventory->exists ? $inventory->stock : 0) - $qty;
                    $status &= $inventory->save();

                    $inventoryDetail = new InventoryRecycleDetailModel([
                        'inventory_id' => $inventory->id,
                        'distributor_shop_id' => $salesOrder->vendor,
                        'battery_recycle_id' => $battery->battery_id,
                        'type' => 'out',
                        'reference' => 'Sales Order Battery',
                        'quantity' => -$qty,
                        'note' => 'Sales Order Battery - ' . $salesOrder->sales_order_number,
                    ]);

                    if (method_exists($inventoryDetail, 'reference')) {
                        $inventoryDetail->reference()->associate($battery);
                    }
                    $status &= $inventoryDetail->save();

                    $logMessage .= "Function sendToInventorySystem: Processed recycle battery ID " . $battery->battery_id . "\n";
                } else {
                    $inventory = InventoryModel::firstOrNew([
                        'battery_id' => $battery->battery_id
                    ]);

                    $inventory->stock = ($inventory->exists ? $inventory->stock : 0) - $qty;
                    $status &= $inventory->save();

                    $inventoryDetail = new InventoryDetailModel([
                        'inventory_id' => $inventory->id,
                        'battery_id' => $battery->battery_id,
                        'type' => 'out',
                        'reference' => 'Sales Order Battery',
                        'quantity' => -$qty,
                        'note' => 'Sales Order Battery - ' . $salesOrder->sales_order_number,
                    ]);

                    if (method_exists($inventoryDetail, 'reference')) {
                        $inventoryDetail->reference()->associate($battery);
                    }
                    $status &= $inventoryDetail->save();

                    $logMessage .= "Function sendToInventorySystem: Processed regular battery ID " . $battery->battery_id . "\n";
                }
            }

            if (!$status) {
                $logMessage .= "Function sendToInventorySystem: Error occurred while processing Sales Order ID " . $salesOrder->id . " - Sales Order Number " . $salesOrder->sales_order_number . "\n";
                Logger::error($logMessage);
            } else {
                Logger::info($logMessage);
                $logMessage = '';
            }
        }
        return $status;
    }

    public function ship_to(): BelongsTo
    {
        return $this->morphTo('ship_to', 'ship_to_type', 'ship_to_id');
    }


    public static function sendToInventorySystemSalesBilling($salesOrderIds)
    {
        $salesOrders = self::with('batteries')->whereIn('id', $salesOrderIds)->get();

        $status = true;
        $logMessage = '';
        $logMessage .= "Function sendToInventorySystemSalesBilling: Starting inventory update for Sales Order IDs: " . implode(', ', $salesOrderIds) . "\n";
        foreach ($salesOrders as $salesOrder) {
            $logMessage .= "Function sendToInventorySystemSalesBilling: Processing Sales Order ID " . $salesOrder->id . " with Sales Order Number " . $salesOrder->sales_order_number . "\n";
            $salesOrderBattery = $salesOrder->batteries;
            foreach ($salesOrderBattery as $battery) {
                $logMessage .= "Function sendToInventorySystemSalesBilling: Processing Battery ID " . $battery->battery_id . " of type " . $battery->type . " with quantity " . $battery->quantity . "\n";
                $checkType = $battery->type;
                $qty = $battery->quantity;
                if ($checkType == 'recycle') {
                    $inventory = InventoryRecycleModel::firstOrNew([
                        'battery_recycle_id' => $battery->battery_id
                    ]);

                    $inventory->stock = ($inventory->exists ? $inventory->stock : 0) - $qty;
                    $status &= $inventory->save();

                    $inventoryDetail = new InventoryRecycleDetailModel([
                        'inventory_id' => $inventory->id,
                        'distributor_shop_id' => $salesOrder->vendor,
                        'battery_recycle_id' => $battery->battery_id,
                        'type' => 'out',
                        'reference' => 'Sales Order Battery',
                        'quantity' => -$qty,
                        'note' => 'Sales Order Battery - ' . $salesOrder->sales_order_number,
                    ]);

                    if (method_exists($inventoryDetail, 'reference')) {
                        $inventoryDetail->reference()->associate($battery);
                    }
                    $status &= $inventoryDetail->save();

                    $logMessage .= "Function sendToInventorySystemSalesBilling: Processed recycle battery ID " . $battery->battery_id . "\n";
                } else {
                    $inventory = InventoryModel::firstOrNew([
                        'battery_id' => $battery->battery_id
                    ]);

                    $inventory->stock = ($inventory->exists ? $inventory->stock : 0) - $qty;
                    $status &= $inventory->save();

                    $inventoryDetail = new InventoryDetailModel([
                        'inventory_id' => $inventory->id,
                        'battery_id' => $battery->battery_id,
                        'type' => 'out',
                        'reference' => 'Sales Order Battery',
                        'quantity' => -$qty,
                        'note' => 'Sales Order Battery - ' . $salesOrder->sales_order_number,
                    ]);

                    if (method_exists($inventoryDetail, 'reference')) {
                        $inventoryDetail->reference()->associate($battery);
                    }
                    $status &= $inventoryDetail->save();

                    $logMessage .= "Function sendToInventorySystemSalesBilling: Processed regular battery ID " . $battery->battery_id . "\n";
                }
            }

            if (!$status) {
                $logMessage .= "Function sendToInventorySystemSalesBilling: Error occurred while processing Sales Order ID " . $salesOrder->id . " - Sales Order Number " . $salesOrder->sales_order_number . "\n";
                Logger::error($logMessage);
            } else {
                Logger::info($logMessage);
                $logMessage = '';
            }
        }
        return $status;
    }

    public static function sendToInventorySystemPurchaseBilling($salesOrderIds)
    {
        $salesOrders = self::with('batteries')->whereIn('id', $salesOrderIds)->get();

        $status = true;
        $logMessage = '';
        $logMessage .= "Function sendToInventorySystemPurchaseBilling: Starting inventory update for Sales Order IDs: " . implode(', ', $salesOrderIds) . "\n";
        foreach ($salesOrders as $salesOrder) {
            $logMessage .= "Function sendToInventorySystemPurchaseBilling: Processing Sales Order ID " . $salesOrder->id . " with Sales Order Number " . $salesOrder->sales_order_number . "\n";
            $salesOrderBattery = $salesOrder->batteries;
            foreach ($salesOrderBattery as $battery) {
                $logMessage .= "Function sendToInventorySystemPurchaseBilling: Processing Battery ID " . $battery->battery_id . " of type " . $battery->type . " with quantity " . $battery->quantity . "\n";
                $checkType = $battery->type;
                $qty = $battery->quantity;
                if ($checkType == 'recycle') {
                    $inventory = InventoryRecycleModel::firstOrNew([
                        'battery_recycle_id' => $battery->battery_id
                    ]);

                    $inventory->stock = ($inventory->exists ? $inventory->stock : 0) - $qty;
                    $status &= $inventory->save();

                    $inventoryDetail = new InventoryRecycleDetailModel([
                        'inventory_id' => $inventory->id,
                        'distributor_shop_id' => $salesOrder->vendor,
                        'battery_recycle_id' => $battery->battery_id,
                        'type' => 'in',
                        'reference' => 'Sales Order Battery',
                        'quantity' => +$qty,
                        'note' => 'Sales Order Battery - ' . $salesOrder->sales_order_number,
                    ]);

                    if (method_exists($inventoryDetail, 'reference')) {
                        $inventoryDetail->reference()->associate($battery);
                    }
                    $status &= $inventoryDetail->save();
                    $logMessage .= "Function sendToInventorySystemPurchaseBilling: Processed recycle battery ID " . $battery->battery_id . "\n";
                } else {
                    $inventory = InventoryModel::firstOrNew([
                        'battery_id' => $battery->battery_id
                    ]);

                    $inventory->stock = ($inventory->exists ? $inventory->stock : 0) - $qty;
                    $status &= $inventory->save();

                    $inventoryDetail = new InventoryDetailModel([
                        'inventory_id' => $inventory->id,
                        'battery_id' => $battery->battery_id,
                        'type' => 'in',
                        'reference' => 'Sales Order Battery',
                        'quantity' => +$qty,
                        'note' => 'Sales Order Battery - ' . $salesOrder->sales_order_number,
                    ]);

                    if (method_exists($inventoryDetail, 'reference')) {
                        $inventoryDetail->reference()->associate($battery);
                    }
                    $status &= $inventoryDetail->save();
                    $logMessage .= "Function sendToInventorySystemPurchaseBilling: Processed regular battery ID " . $battery->battery_id . "\n";
                }
            }

            if (!$status) {
                $logMessage .= "Function sendToInventorySystemPurchaseBilling: Error occurred while processing Sales Order ID " . $salesOrder->id . " - Sales Order Number " . $salesOrder->sales_order_number . "\n";
                Logger::error($logMessage);
            } else {
                Logger::info($logMessage);
                $logMessage = '';
            }
        }
        return $status;
    }

    public function billingInvoiceExpenses()
    {
        return $this->hasMany(BillingInvoiceExpenseModel::class, 'sales_order_id')->with('debitAccount');
    }
}
