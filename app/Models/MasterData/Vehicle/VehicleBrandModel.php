<?php

namespace App\Models\MasterData\Vehicle;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleBrandModel extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vehicle_brands';

    /**
     * The list of columns in the associated table.
     */
    private static $selectColumns = ['id', 'name'];

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
        $query = self::query();
        $query->select(self::$selectColumns);

        // Searching process.
        if ($searchValue != null) {
            $query->where(function ($query) use ($searchValue) {
                foreach (self::$selectColumns as $column) {
                    $query->orWhere($column, "LIKE", "%" . $searchValue . "%");
                }
            });
        }

        // Ordering process.
        if ($orderColumn !== null) {
            $columnName = self::$selectColumns[$orderColumn] ?? null;
            if ($columnName !== null) {
                $query->orderBy($columnName, $orderDirection);
            }
        }

        return array(
            "count" => $query->count(),
            "row" => $query->orderBy("name", "ASC")
                ->skip($start)
                ->take($length)
                ->get(),
        );
    }
}
