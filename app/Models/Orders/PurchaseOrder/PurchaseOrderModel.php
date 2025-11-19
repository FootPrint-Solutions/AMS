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
        'vendor_id',
        'vendor_type',
        'ship_to_id',
        'ship_to_type',
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
     * Get the vendor associated with the purchase order.
     */
    public function vendor(): BelongsTo
    {
        return $this->morphTo();
    }

    /**
     * Get the ship to associated with the purchase order.
     */
    public function shipTo(): BelongsTo
    {
        return $this->morphTo();
    }

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
     * Get all of the batteries of the purchase order.
     */
    public function details(): HasMany
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
        $query = self::with(['vendor', 'shipTo']);

        return self::dataTablesQuery($query, $request);
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
