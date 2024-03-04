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
     * @return array Associative array containing data for DataTables display.
     */
    public static function getAllRows($request, $query, $selectColumns)
    {
        // Get DataTables configuration request.
        $start = $request->input("start");
        $length = $request->input("length");
        $searchValue = $request->input("search.value");
        $orderColumn = $request->input("order.0.column");
        $orderDirection = $request->input("order.0.dir");

        // Searching process.
        if ($searchValue != null) {
            $query->where(function ($query) use ($searchValue, $selectColumns) {
                foreach ($selectColumns as $column) {
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
        }

        return array(
            "count" => $query->count(),
            "row" => $query->skip($start)
                ->take($length)
                ->get(),
        );
    }
}
