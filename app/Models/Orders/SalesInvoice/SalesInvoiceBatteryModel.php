<?php

namespace App\Models\Orders\SalesInvoice;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// TRAITS
use App\Traits\DataTablesTrait;
use App\Models\MasterData\Battery\BatteryModel;

class SalesInvoiceBatteryModel extends Model
{
    use HasFactory, DataTablesTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sales_invoice_batteries';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sales_invoice_id',
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
        'image'
    ];

    /**
     * The list of columns in the associated table.
     */
    private static $selectColumns = [
        'id',
        'sales_invoice_id',
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
        'image'
    ];

    /**
     * Get all data for DataTables.
     * 
     * @param \Illuminate\Http\Request $request The POST request obtained (for DataTables configuration).
     * @return array Associative array containing data for DataTables display.
     */
    public static function whereForDataTables($request)
    {
        $selectColumns = self::$selectColumns;

        $query = self::query()
            ->where('sales_invoice_id', $request->id);
        $query->select($selectColumns);

        return self::getAllRows($request, $query, $selectColumns, $selectColumns);
    }

    /**
     * Get the battery associated with the sales invoice.
     */
    public function battery()
    {
        return $this->belongsTo(BatteryModel::class, 'battery_id', 'id');
    }

    /**
     * Get the sales invoice associated with the battery.
     */
    public function salesInvoice()
    {
        return $this->belongsTo(SalesInvoiceModel::class, 'sales_invoice_id', 'id');
    }
}
