<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// TRAITS
use App\Traits\DataTablesTrait;

// MODELS
use App\Models\Settings\PrintTemplateDetailModel;

class PrintTemplateModel extends Model
{
    use HasFactory, DataTablesTrait;

    protected $table = 'work_order_print_template_master';

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

        return self::getAllRows($request, $query, $selectColumns, $searchColumns, ['column' => 'name', 'direction' => 'desc']);
    }

    /**
     * Get the details for the print template.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany The relationship between the print template and its details.
     */
    public function details()
    {
        return $this->hasMany(PrintTemplateDetailModel::class, 'work_order_print_template_master_id')
            ->selectRaw('work_order_print_template_details.*, (SELECT COUNT(*) FROM work_order_print_template_details_sub WHERE work_order_print_template_details_id = work_order_print_template_details.id) as sub_details_count')
            ->with('subDetails');
    }
}
