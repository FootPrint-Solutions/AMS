<?php

namespace App\Models\MasterData\Distributor;

use App\Models\MasterData\Battery\BatteryModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

// TRAITS
use App\Traits\DataTablesTrait;
use OwenIt\Auditing\Auditable as AuditableTrait;

class DistributorShopModel extends Model implements Auditable
{
    use HasFactory, SoftDeletes, DataTablesTrait, AuditableTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'distributor_shops';

    /**
     * Get distributor shop.
     */
    public function distributor(): BelongsTo
    {
        return $this->belongsTo(DistributorModel::class, 'distributor_id');
    }

    /**
     * Get all of the technicians worked in the distributor shop.
     */
    public function technicians()
    {
        return $this->hasMany(DistributorShopTechnicianModel::class, 'distributor_shop_id', 'id');
    }

    /**
     * Get all of the specific batteries of the distributor shop.
     */
    public function batteries()
    {
        return $this->belongsToMany(BatteryModel::class, 'distributor_shop_model', 'distributor_shop_id', 'battery_id')
            ->withTimestamps();
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
        $selectColumns = ['id', 'name', 'address', 'contact_person', 'contact', 'email', 'distributor_id', 'status'];
        $searchColumns = ['name', 'address', 'contact_person', 'contact', 'email', 'distributor_id'];

        // Build the query to obtain all rows.
        $query = self::query();
        $query->select($selectColumns);

        return self::getAllRows($request, $query, $selectColumns, $searchColumns);
    }
}
