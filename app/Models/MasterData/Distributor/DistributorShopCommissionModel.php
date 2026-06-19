<?php

namespace App\Models\MasterData\Distributor;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

// TRAITS
use App\Traits\DataTablesTrait;
use OwenIt\Auditing\Auditable as AuditableTrait;

use App\Models\MasterData\Distributor\DistributorShopModel;
use App\Models\MasterData\Battery\BatteryModel;

class DistributorShopCommissionModel extends Model implements Auditable
{
    use HasFactory, DataTablesTrait, AuditableTrait;

    protected $table = 'distributor_shop_commission';

    protected $fillable = [
        'distributor_shop_id',
        'battery_id',
        'type',
        'commission',
    ];

    protected $casts = [
        'id' => 'integer',
        'distributor_shop_id' => 'integer',
        'battery_id' => 'integer',
        'commission' => 'double',
    ];

    public function distributorShop()
    {
        return $this->belongsTo(DistributorShopModel::class, 'distributor_shop_id');
    }

    public function battery()
    {
        return $this->belongsTo(BatteryModel::class, 'battery_id');
    }

    /**
     * Get all data for DataTables.
     * 
     * @param \Illuminate\Http\Request $request The POST request obtained (for DataTables configuration).
     * @return array Associative array containing data for DataTables display.
     */
    public static function whereForDataTables($request)
    {
        $selectColumns = [
            'distributor_shop_commission.id',
            'batteries.name as battery_name',
            'distributor_shop_commission.type',
            'distributor_shop_commission.commission',
            'distributor_shop_commission.battery_id'
        ];
        $searchColumns = ['batteries.name', 'distributor_shop_commission.type'];

        // Build the query to obtain all rows.
        $query = self::query()
            ->join('batteries', 'batteries.id', '=', 'distributor_shop_commission.battery_id')
            ->where('distributor_shop_commission.distributor_shop_id', $request->shop_id);
        $query->select($selectColumns);

        return self::getAllRows($request, $query, $selectColumns, $searchColumns);
    }
}
