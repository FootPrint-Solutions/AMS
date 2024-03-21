<?php

namespace App\Models\Orders\Quotation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// TRAITS
use App\Traits\DataTablesTrait;
use Illuminate\Support\Facades\DB;

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
