<?php

namespace App\Models\MasterData\Distributor;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// TRAITS
use App\Traits\DataTablesTrait;

class DistributorShopBatteryModel extends Model
{
    use HasFactory, DataTablesTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'distributor_shop_battery';

    /**
     * The list of columns in the associated table.
     */
    private static $selectColumns = [
        'distributor_shop_battery.id', 'batteries.name', 'price', 'url'
    ];

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
            ->join('distributor_shops', 'distributor_shops.id', '=', 'distributor_shop_battery.distributor_shop_id')
            ->join('batteries', 'batteries.id', '=', 'distributor_shop_battery.battery_id')
            ->where('distributor_shop_id', $request->id);
        $query->select(self::$selectColumns);

        return self::getAllRows($request, $query, self::$selectColumns);
    }
}
