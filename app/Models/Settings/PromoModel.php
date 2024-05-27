<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// TRAITS
use App\Traits\DataTablesTrait;

class PromoModel extends Model
{
    use HasFactory, SoftDeletes, DataTablesTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'battery_prices';

    /**
     * Get all data for DataTables.
     * 
     * @param \Illuminate\Http\Request $request The POST request obtained (for DataTables configuration).
     * @return array Associative array containing data for DataTables display.
     */
    public static function allForDataTables($request)
    {
        // Set the list of select and search columns.
        $selectColumns = ['battery_prices.id', 'batteries.name as battery_name', 'batteries.price_retail', 'discount', 'net_price', 'period'];
        $searchColumns = ['batteries.name'];

        // Build the query to obtain all rows.
        $query = self::query();
        $query->leftJoin("batteries", "battery_prices.battery_id", "=", "batteries.id");
        $query->select($selectColumns);

        return self::getAllRows($request, $query, $selectColumns, $searchColumns, ['column' => 'battery_prices.updated_at', 'direction' => 'desc']);
    }
}
