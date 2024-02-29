<?php

namespace App\Models\MasterData\Distributor;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

// TRAITS
use App\Traits\DataTablesTrait;

class DistributorShopModel extends Model
{
    use HasFactory, SoftDeletes, DataTablesTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'distributor_shops';

    /**
     * The list of columns in the associated table.
     */
    private static $selectColumns = [
        'id', 'name', 'address', 'contact_person', 'contact', 'email', 'distributor_id'
    ];

    /**
     * Get distributor shop.
     */
    public function distributor(): BelongsTo
    {
        return $this->belongsTo(DistributorModel::class, 'distributor_id');
    }

    /**
     * Get all data for DataTables.
     * 
     * @param int $start The starting index of rows.
     * @param int $length The number of rows to be returned.
     * @param string $searchValue The search filter value.
     * @param int $orderColumn The column index for ordering.
     * @param int $orderDirection Ascending or descending order.
     * @return array Associative array containing data for DataTables display.
     */
    public static function allForDataTables($start, $length, $searchValue, $orderColumn, $orderDirection)
    {
        return self::getAllRows($start, $length, $searchValue, $orderColumn, $orderDirection, self::$selectColumns);
    }
}
