<?php

namespace App\Models\MasterData\Battery;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

// TRAITS
use App\Traits\DataTablesTrait;
use OwenIt\Auditing\Auditable as AuditableTrait;

// MODEL
use App\Models\MasterData\Battery\BatteryModel;

class BatterySizeCategoryModel extends Model implements Auditable
{
    use HasFactory, SoftDeletes, DataTablesTrait, AuditableTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'battery_size_categories';

    protected $fillable = [
        'name',
    ];

    /**
     * Get all data for DataTables.
     * 
     * @param \Illuminate\Http\Request $request The POST request obtained (for DataTables configuration).
     * @return array Associative array containing data for DataTables display.
     */
    public static function allForDataTables($request)
    {
        // Set the list of select and search columns.
        $selectColumns = ['id', 'name'];
        $searchColumns = ['name'];

        // Build the query to obtain all rows.
        $query = self::query();
        $query->select($selectColumns);

        return self::getAllRows($request, $query, $selectColumns, $searchColumns);
    }

    /**
     * Get the batteries associated with the battery size category.
     *
     * This function defines a one-to-many relationship between the BatterySizeCategoryModel
     * and the BatteryModel. It returns all BatteryModel instances that have a foreign key
     * 'size_category_id' matching the primary key of the BatterySizeCategoryModel.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function batteries()
    {
        return $this->hasMany(BatteryModel::class, 'size_category_id')
            ->where('status', 1)
            ->with('batteryUrl', 'batteryPrices');
    }
}
