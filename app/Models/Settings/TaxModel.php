<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// TRAITS
use App\Traits\DataTablesTrait;

class TaxModel extends Model
{
    use HasFactory, SoftDeletes, DataTablesTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'taxes';

    /**
     * Get the updated order and update the order of other menu items within its parent.
     *
     * @param int|null $menuId The ID of the menu positioned after the current menu.
     * @param int $parentId The ID of the parent menu.
     * @param int|null $originalOrder The original order of the menu being moved (optional).
     * @return int The new order of the menu item.
     */
    public function status()
    {
        self::where("status", "active")
            ->update([
                "status" => "inactive",
                "updated_at" => now()->toDateTimeString()
            ]);
        return "active";
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
        $selectColumns = ['id', 'percentage', 'valid_until', 'status'];
        $searchColumns = ['percentage', 'valid_until', 'status'];

        // Build the query to obtain all rows.
        $query = self::query();
        $query->select($selectColumns);

        return self::getAllRows($request, $query, $selectColumns, $searchColumns, ['column' => 'status', 'direction' => 'asc']);
    }
}
