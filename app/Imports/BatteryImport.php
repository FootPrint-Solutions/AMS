<?php

namespace App\Imports;

use App\Models\MasterData\Battery\BatteryBrandModel;
use App\Models\MasterData\Battery\BatteryModel;
use App\Models\MasterData\Battery\BatterySizeCategoryModel;
use App\Models\MasterData\Battery\BatteryTechnologyModel;
use App\Models\MasterData\Battery\BatteryUsageTypeModel;
use App\Models\MasterData\Battery\BatterySubbrandCategoryModel;


use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class BatteryImport implements ToModel, WithStartRow
{
    /**
     * @return int
     */
    public function startRow(): int
    {
        return 4;
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {

        if (!$this->validateRow($row)) {
            return null;
        }

        $brand = BatteryBrandModel::firstOrCreate(['name' => $row[2]]);
        $sizeCategory = BatterySizeCategoryModel::firstOrCreate(['name' => $row[6]]);
        $usageType = BatteryUsageTypeModel::firstOrCreate(['name' => $row[4]]);
        $technology = BatteryTechnologyModel::firstOrCreate(['name' => $row[5]]);
        $SubbrandCategory = BatterySubbrandCategoryModel::firstOrCreate(['name' => $row[3]]);

        $standardCca = $row[10] != '-' ? $row[10] : 0;
        $capacity = $row[11] != '-' ? $row[11] : null;
        $warranty = $row[12] != '-' ? $row[12] : 0;
        $priceRetail = $row[13] != '-' ? $row[13] : null;

        $Battery = BatteryModel::create([
            'name' => $row[0],
            'id_brand' => $brand->id,
            'id_subbrand_category' => $SubbrandCategory->id,
            'id_usage_type' => $usageType->id,
            'id_size_category' => $sizeCategory->id,
            'id_technology' => $technology->id,
            'dimension_length' => $row[7] ?? null,
            'dimension_width' => $row[8] ?? null,
            'dimension_height' => $row[9] ?? null,
            'standard_cca' => $standardCca,
            'capacity' => $capacity,
            'warranty' => $warranty ?? 0,
            'price_retail' => $priceRetail,
        ]);

        return $Battery;
    }

    /**
     * Validate the row data
     *
     * @param array $row
     * @return bool
     */
    private function validateRow(array $row): bool
    {
        if (empty($row[1]) || empty($row[4]) || empty($row[5]) || empty($row[6])) {
            return false; // Validation failed
        }

        return true;
    }
}
