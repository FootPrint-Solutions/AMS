<?php

namespace App\Models\Orders\PurchaseOrder;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

// TRAITS
use App\Traits\DataTablesTrait;
use OwenIt\Auditing\Auditable as AuditableTrait;

// MODELS
use App\Models\MasterData\Supplier\SupplierModel;

class PurchaseOrderModel extends Model implements Auditable
{
    use HasFactory, SoftDeletes, DataTablesTrait, AuditableTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'purchase_orders';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'purchase_order_number',
        'invoice_number',
        'date',
        'supplier_id',
        'ship_to',
        'discount_price',
        'subtotal',
        'total',
        'payment_status',
        'status',
        'address',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'date' => 'date',
        'discount_price' => 'double',
        'subtotal' => 'double',
        'total' => 'double',
    ];

    /**
     * Get the supplier associated with the purchase order.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(SupplierModel::class, 'supplier_id');
    }

    /**
     * Get all of the batteries of the purchase order.
     */
    public function batteries(): HasMany
    {
        return $this->hasMany(PurchaseOrderBatteryModel::class, 'purchase_order_id')
            ->with('battery');
    }

    /**
     * Get all data for DataTables.
     * 
     * @param \Illuminate\Http\Request $request The POST request obtained (for DataTables configuration).
     * @return array Associative array containing data for DataTables display.
     */
    public static function allForDataTables($request)
    {
        $selectColumns = [
            'purchase_orders.id',
            'purchase_orders.purchase_order_number',
            'purchase_orders.invoice_number',
            'purchase_orders.date',
            'suppliers.name as supplier_name',
            'distributor_shops.name as shop_name',
            'purchase_orders.subtotal',
            'purchase_orders.discount_price',
            'purchase_orders.total',
            'purchase_orders.payment_status',
            'purchase_orders.status',
        ];

        $searchColumns = [
            'purchase_order_number',
            'invoice_number',
            'suppliers.name',
            'payment_status',
            'purchase_orders.status',
            'distributor_shops.name',
        ];

        $orderColumns = [
            'id',
            'purchase_order_number',
            'invoice_number',
            'date',
            'supplier_name',
            'subtotal',
            'discount_price',
            'total',
            'payment_status',
            'purchase_orders.status',
            'shop_name',
        ];

        $query = self::query()
            ->leftJoin('suppliers', 'purchase_orders.supplier_id', '=', 'suppliers.id')
            ->leftJoin('distributor_shops', 'purchase_orders.ship_to', '=', 'distributor_shops.id');

        // Filter by status if provided
        if ($request->has('status') && $request->status !== null && $request->status !== '' && $request->status !== 'all') {
            $query->where('purchase_orders.status', $request->status);
        }

        // Filter by supplier_id if provided
        if ($request->has('supplier_id') && $request->supplier_id !== null && $request->supplier_id !== '' && $request->supplier_id !== 'all') {
            $query->where('purchase_orders.supplier_id', $request->supplier_id);
        }

        // Filter by date range if provided
        if ($request->has('dateStart') && $request->has('dateEnd') && $request->dateStart && $request->dateEnd) {
            $query->whereBetween('purchase_orders.date', [$request->dateStart, $request->dateEnd]);
        }

        // Filter by distributor shop if provided
        if ($request->has('distributor_shop_id') && $request->distributor_shop_id !== null && $request->distributor_shop_id !== '' && $request->distributor_shop_id !== 'all') {
            $query->where('purchase_orders.ship_to', $request->distributor_shop_id);
        }

        $query->select($selectColumns);

        // DataTables pagination
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);

        // DataTables search
        $searchValue = $request->input('search.value');
        if ($searchValue) {
            $query->where(function ($q) use ($searchColumns, $searchValue) {
                foreach ($searchColumns as $col) {
                    $q->orWhere($col, 'like', '%' . $searchValue . '%');
                }
            });
        }

        // DataTables ordering
        $order = $request->input('order');
        if ($order && isset($order[0])) {
            $orderColIdx = $order[0]['column'];
            $orderDir = $order[0]['dir'];
            $orderCol = $orderColumns[$orderColIdx] ?? 'id';
            $query->orderBy($orderCol, $orderDir);
        } else {
            $query->orderBy('purchase_orders.id', 'desc');
        }

        $countQuery = clone $query;
        $count = $countQuery->count();

        $rows = $query->skip($start)->take($length)->get();

        return [
            'row' => $rows,
            'count' => $count,
        ];
    }

    /**
     * Generate purchase order number.
     *
     * @return string
     */
    public static function generatePurchaseOrderNumber()
    {
        $prefix = 'KP';
        $yearShort = date('y');
        $yearFull = date('Y');
        $month = date('m');

        // Get the latest purchase order for the current month and year
        $latestOrder = self::whereYear('created_at', $yearFull)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();

        if ($latestOrder && strlen($latestOrder->purchase_order_number) >= 6) {
            // Extract the last 6 digits as the sequence number
            $lastNumber = (int) substr($latestOrder->purchase_order_number, -6);
            $newNumber = str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '000001';
        }

        return $prefix . $yearShort . $month . $newNumber;
    }
}
