<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

// TRAITS
use App\Traits\DataTablesTrait;
use Illuminate\Support\Facades\DB;

class PromoModel extends Model
{
    use HasFactory, SoftDeletes, DataTablesTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'promos';

    /**
     * Get all of the batteries of the quotations.
     */
    public function batteries(): HasMany
    {
        return $this->hasMany(PromoBatteryModel::class, "promo_id")
            ->join('batteries', 'promo_battery.battery_id', '=', 'batteries.id')
            ->select('promo_battery.*', 'batteries.name');
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
        $selectColumns = ['id', 'name', 'period_start', 'period_end', 'status'];
        $searchColumns = ['name'];

        // Build the query to obtain all rows.
        $query = self::query();
        $query->select($selectColumns);

        return self::getAllRows($request, $query, $selectColumns, $searchColumns);
    }

    /**
     * Get all data for DataTables.
     * 
     * @param \Illuminate\Http\Request $request The POST request obtained (for DataTables configuration).
     * @return array Associative array containing data for DataTables display.
     */
    public static function allForDataTablesDashboard($request, $type)
    {
        // Set the list of select and search columns.
        $selectColumns = ['id', 'name', 'period_end', 'status'];
        $searchColumns = ['name'];

        // Build the query to obtain all rows.
        $query = self::query();
        $query->select($selectColumns);
        $query->where('status', 1);

        if ($type == 'unlimited') {
            // Get only unlimited promos.
            $query->where('period_end', '=', null);
        } else if ($type == 'limited') {
            // Get only limited promos.
            $query->where('period_end', '>=', Carbon::today());
        }

        // Get the list of discounted batteries.
        $query->addSelect(DB::raw(
            '(
                SELECT IF(COUNT(*) > 2, CONCAT(GROUP_CONCAT(SUBSTRING_INDEX(batteries.name, ",", 3) SEPARATOR ", "), ", and more..."), GROUP_CONCAT(batteries.name SEPARATOR ", ")) 
                FROM batteries 
                INNER JOIN promo_battery ON batteries.id = promo_battery.battery_id 
                WHERE promo_battery.promo_id = promos.id) AS battery_list'
        ));

        return self::getAllRows($request, $query, $selectColumns, $searchColumns, ['column' => 'period_end', 'direction' => 'asc']);
    }
}
