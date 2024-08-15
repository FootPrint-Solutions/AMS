<?php

namespace App\Traits;

trait DataTablesTrait
{
    /**
     * Get all data for DataTables.
     * 
     * @param \Illuminate\Http\Request $request The POST request obtained (for DataTables configuration).
     * @param Illuminate\Database\Query\Builder $query Query built to obtain rows in table.
     * @param array $selectColumns The list of columns to be displayed.
     * @param array $searchColumns The list of columns to be searchable.
     * @param array $searchColumns An array consists of two values : the default column order and the order direction (if null, table will be ordered by the update time in descending order by default).
     * @return array Associative array containing data for DataTables display.
     */

    public static function getAllRows($request, $query, $selectColumns, $searchColumns = null, $orderDefault = null)
    {
        // Get DataTables configuration request.
        $start = $request->input("start");
        $length = $request->input("length");
        $searchValue = $request->input("search.value");
        $orderColumn = $request->input("order.0.column");
        $orderDirection = $request->input("order.0.dir");

        // Searching process.
        if ($searchColumns != null && $searchValue != null) {
            $query->where(function ($query) use ($searchValue, $searchColumns) {
                foreach ($searchColumns as $column) {
                    $query->orWhere($column, "LIKE", "%" . $searchValue . "%");
                }
            });
        }

        // Ordering process.
        if ($orderColumn !== null) {
            $columnName = $selectColumns[$orderColumn] ?? null;
            if ($columnName !== null) {
                $query->orderBy($columnName, $orderDirection);
            }
        } else {
            if ($orderDefault !== null) {
                $query->orderBy($orderDefault["column"], $orderDefault["direction"]);
            } else {
                $query->orderBy("updated_at", "desc");
            }
        }

        return array(
            "count" => $query->count(),
            "row" => $query->skip($start)
                ->take($length)
                ->get(),
        );
    }
}
