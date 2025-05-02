<?php

namespace App\Models\Orders\SalesOrder;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// TRAITS
use App\Traits\DataTablesTrait;
use App\Models\MasterData\Battery\BatteryModel;

class SalesOrderBatteryModel extends Model
{
    use HasFactory, DataTablesTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sales_order_battery';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sales_order_id',
        'battery_id',
        'battery_name',
        'battery_price',
        'battery_price_retail',
        'battery_production_code',
        'quantity'
    ];

    /**
     * The list of columns in the associated table.
     */
    private static $selectColumns = ['id', 'sales_order_id', 'battery_id', 'battery_name', 'battery_price', 'quantity', 'battery_production_code'];

    /**
     * Get all data for DataTables.
     * 
     * @param \Illuminate\Http\Request $request The POST request obtained (for DataTables configuration).
     * @return array Associative array containing data for DataTables display.
     */
    public static function whereForDataTables($request)
    {
        // Set the list of select and search columns.
        $selectColumns = ['id', 'sales_order_id', 'battery_id', 'battery_name', 'battery_price', 'quantity', 'battery_production_code'];

        // Build the query to obtain all rows.
        $query = self::query()
            ->where('sales_order_id', $request->id);
        $query->select(self::$selectColumns);

        return self::getAllRows($request, $query, $selectColumns, $selectColumns);
    }

    /**
     * Get the battery associated with the sales order.
     */
    public function battery()
    {
        return $this->belongsTo(BatteryModel::class, 'battery_id', 'id');
    }
}
