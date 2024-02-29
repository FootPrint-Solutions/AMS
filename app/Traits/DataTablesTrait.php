<?php

namespace App\Traits;

trait DataTablesTrait
{
    /**
     * Get all data for DataTables.
     * 
     * @param int $start The starting index of rows.
     * @param int $length The number of rows to be returned.
     * @param string $searchValue The search filter value.
     * @param int $orderColumn The column index for ordering.
     * @param int $orderDirection Ascending or descending order.
     * @param array $selectColumns The list of columns to be displayed.
     * @return array Associative array containing data for DataTables display.
     */
    public static function getAllRows($start, $length, $searchValue, $orderColumn, $orderDirection, $selectColumns)
    {
        $query = self::query();
        $query->select($selectColumns);

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
            "row" => $query->orderBy("name", "ASC")
                ->skip($start)
                ->take($length)
                ->get(),
        );
    }
}
