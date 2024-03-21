<?php

namespace App\Models\Orders\Quotation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

// TRAITS
use App\Traits\DataTablesTrait;

// CONTROLLER
use App\Models\MasterData\Customer\CustomerModel;
use App\Models\MasterData\Distributor\DistributorShopModel;
use App\Models\MasterData\Distributor\DistributorShopTechnicianModel;

class QuotationModel extends Model
{
    use HasFactory, SoftDeletes, DataTablesTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'quotations';

    /**
     * The list of columns in the associated table.
     */
    private static $selectColumns = ['*'];

    /**
     * Get the customer of the quotations.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(CustomerModel::class);
    }

    /**
     * Get the distributor shop of the quotations.
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(DistributorShopModel::class, "distributor_shop_id");
    }

    /**
     * Get the distributor shop technician of the quotations.
     */
    public function technician(): BelongsTo
    {
        return $this->belongsTo(DistributorShopTechnicianModel::class, "distributor_shop_technician_id");
    }

    /**
     * Get all of the batteries of the quotations.
     */
    public function batteries(): HasMany
    {
        return $this->hasMany(QuotationBatteryModel::class, "quotation_id");
    }

    /**
     * Get all data for DataTables.
     * 
     * @param \Illuminate\Http\Request $request The POST request obtained (for DataTables configuration).
     * @return array Associative array containing data for DataTables display.
     */
    public static function allForDataTables($request)
    {
        // Build the query to obtain all rows.
        $query = DB::table('quotations_view');
        $query->select(self::$selectColumns);
        $query->whereNull("deleted_at");

        return self::getAllRows($request, $query, self::$selectColumns);
    }
}
