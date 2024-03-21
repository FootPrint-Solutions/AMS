<?php

namespace App\Models\Orders\Quotation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// TRAITS
use App\Traits\DataTablesTrait;

class QuotationBatteryModel extends Model
{
    use HasFactory, DataTablesTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'quotation_battery';

    /**
     * The list of columns in the associated table.
     */
    private static $selectColumns = ['quotation_id', 'battery_id', 'battery_name', 'battery_price', 'quantity'];

    /**
     * Get all data for DataTables.
     * 
     * @param \Illuminate\Http\Request $request The POST request obtained (for DataTables configuration).
     * @return array Associative array containing data for DataTables display.
     */
    public static function whereForDataTables($request)
    {
        // Build the query to obtain all rows.
        $query = self::query()
            ->where('quotation_id', $request->id);
        $query->select(self::$selectColumns);

        return self::getAllRows($request, $query, self::$selectColumns);
    }
}
