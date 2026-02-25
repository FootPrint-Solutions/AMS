<?php

namespace App\Models\Orders\PurchaseOrder;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// TRAITS
use App\Traits\DataTablesTrait;
use App\Models\MasterData\Battery\BatteryModel;
use App\Models\Orders\SalesOrder\SalesOrderBatteryModel;

class PurchaseOrderBatteryModel extends Model
{
    use HasFactory, DataTablesTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'purchase_order_batteries';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'purchase_order_id',
        'sales_order_battery_id',
        'source',
        'battery_id',
        'battery_name',
        'battery_price_retail',
        'tax',
        'tax_price',
        'discount',
        'discount_price',
        'price_net',
        'quantity',
        'battery_production_code',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'battery_price_retail' => 'double',
        'tax' => 'decimal:2',
        'tax_price' => 'double',
        'discount' => 'decimal:2',
        'discount_price' => 'double',
        'price_net' => 'double',
        'quantity' => 'double',
    ];

    /**
     * The list of columns in the associated table.
     */
    private static $selectColumns = [
        'id',
        'purchase_order_id',
        'battery_id',
        'battery_name',
        'battery_price_retail',
        'tax',
        'tax_price',
        'discount',
        'discount_price',
        'price_net',
        'quantity',
        'battery_production_code'
    ];

    /**
     * Get all data for DataTables.
     * 
     * @param \Illuminate\Http\Request $request The POST request obtained (for DataTables configuration).
     * @return array Associative array containing data for DataTables display.
     */
    public static function whereForDataTables($request)
    {
        // Set the list of select and search columns.
        $selectColumns = ['id', 'purchase_order_id', 'battery_id', 'battery_name', 'price_net', 'quantity', 'battery_production_code'];

        // Build the query to obtain all rows.
        $query = self::query()
            ->where('purchase_order_id', $request->id);
        $query->select(self::$selectColumns);

        return self::getAllRows($request, $query, $selectColumns, $selectColumns);
    }

    /**
     * Get the battery associated with the purchase order.
     */
    public function battery()
    {
        return $this->belongsTo(BatteryModel::class, 'battery_id', 'id');
    }

    /**
     * Get the purchase order associated with the battery.
     */
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrderModel::class, 'purchase_order_id', 'id');
    }

    /**
     * Get the sales order battery associated with the purchase order battery.
     */
    public function batterySalesOrder()
    {
        return $this->belongsTo(SalesOrderBatteryModel::class, 'sales_order_battery_id', 'id');
    }
}
