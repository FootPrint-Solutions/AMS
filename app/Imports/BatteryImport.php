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
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;

class BatteryImport implements ToModel, WithStartRow, WithEvents
{
    private $unimportedRows = [];
    private $totalRows = 0;
    private $totalInsertedRows = 0;

    public function getUnimportedRows()
    {
        return $this->unimportedRows;
    }

    public function getTotalRows()
    {
        return $this->totalRows;
    }

    public function getTotalInsertedRows()
    {
        return $this->totalInsertedRows;
    }

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
        try {
            // Validate the row before processing
            if (!$this->validateRow($row)) {
                // Add invalid row to unimportedRows array
                $this->unimportedRows[] = $row;
                return;
            }

            // Create or find related models
            $brand = BatteryBrandModel::firstOrCreate(['name' => $row[2]]);
            $sizeCategoryId = null;
            if (!empty($row[6]) && $row[6] !== "-") {
                $sizeCategory = BatterySizeCategoryModel::firstOrCreate(['name' => $row[6]]);
                $sizeCategoryId = $sizeCategory->id;
            }
            $usageType = BatteryUsageTypeModel::firstOrCreate(['name' => $row[4]]);
            $technology = BatteryTechnologyModel::firstOrCreate(['name' => $row[5]]);
            $subbrandCategory = BatterySubbrandCategoryModel::firstOrCreate(['name' => $row[3]]);

            // Clean and prepare data for battery creation
            $standardCca = ($row[10] != '-') ? $row[10] : 0;
            $capacity = ($row[11] != '-') ? str_replace(",", ".", $row[11]) : null;
            $warranty = ($row[12] != '-') ? str_replace("bulan", "", $row[12]) : 0;
            $priceRetail = ($row[13] != '-') ? $row[13] : null;

            // Create the Battery model
            $battery = BatteryModel::create([
                'name' => $row[0],
                'name_alternate' => $row[1] ?? '',
                'brand_id' => $brand->id,
                'subbrand_category_id' => $subbrandCategory->id,
                'usage_type_id' => $usageType->id,
                'size_category_id' => $sizeCategoryId,
                'technology_id' => $technology->id,
                'dimension_length' => $row[7] ?? 0,
                'dimension_width' => $row[8] ?? 0,
                'dimension_height' => $row[9] ?? 0,
                'standard_cca' => $standardCca,
                'capacity' => $capacity,
                'warranty' => $warranty ?? 0,
                'price_retail' => $priceRetail,
            ]);

            $this->totalInsertedRows++;
            return $battery;
        } catch (\Exception $e) {
            $this->unimportedRows[] = $row;
            return null;
        }
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

    /**
     * Register events
     *
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function (BeforeImport $event) {
                $worksheet = $event->getDelegate()->getActiveSheet();
                $this->totalRows = $worksheet->getHighestDataRow() - 3;
            },
        ];
    }
}
