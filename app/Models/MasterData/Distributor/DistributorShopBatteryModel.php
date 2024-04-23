<?php

namespace App\Models\MasterData\Distributor;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

// TRAITS
use App\Traits\DataTablesTrait;
use OwenIt\Auditing\Auditable as AuditableTrait;

class DistributorShopBatteryModel extends Model implements Auditable
{
    use HasFactory, DataTablesTrait, AuditableTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'distributor_shop_battery';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['battery_id', 'distributor_shop_id'];

    /**
     * Get all data for DataTables.
     * 
     * @param \Illuminate\Http\Request $request The POST request obtained (for DataTables configuration).
     * @return array Associative array containing data for DataTables display.
     */
    public static function whereForDataTables($request)
    {
        $selectColumns = ['distributor_shop_battery.id', 'batteries.name', 'price', 'url'];
        $searchColumns = ['batteries.name'];

        // Build the query to obtain all rows.
        $query = self::query()
            ->join('distributor_shops', 'distributor_shops.id', '=', 'distributor_shop_battery.distributor_shop_id')
            ->join('batteries', 'batteries.id', '=', 'distributor_shop_battery.battery_id')
            ->where('distributor_shop_id', $request->id);
        $query->select($selectColumns);

        return self::getAllRows($request, $query, $selectColumns, $searchColumns);
    }
}
