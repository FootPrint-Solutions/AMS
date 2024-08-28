<?php

namespace App\Imports;

use App\Models\MasterData\Vehicle\VehicleBrandModel;
use App\Models\MasterData\Vehicle\VehicleModel;
use App\Models\MasterData\Battery\BatteryModel;
use App\Models\MasterData\Battery\BatterySizeCategoryModel;
use App\Models\MasterData\Vehicle\VehicleBatteryModel;
use App\Models\MasterData\Vehicle\VehicleBatterySizeCategoryModel;


use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Events\BeforeImport;

class VehicleImport implements ToModel, WithStartRow, WithEvents
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
                return null;
            }

            // Create or find the brand model
            $brand = VehicleBrandModel::firstOrCreate(['name' => trim($row[1])]);

            // Initialize an array to hold alternate batteries
            $altbatteries = [];
            for ($i = 2; $i <= 5; $i++) {
                if (!empty($row[$i])) {
                    // Create or find battery size category model
                    $battery = BatterySizeCategoryModel::firstOrCreate(['name' => trim($row[$i])]);
                    $altbatteries[] = $battery;
                }
            }

            // Create or find the vehicle model
            $vehicle = VehicleModel::firstOrCreate(
                ['name' => trim($row[0])], // Look for an existing vehicle by name
                ['brand_id' => $brand->id, 'url' => $row[6]]
            );

            // Associate alternate batteries with the vehicle
            foreach ($altbatteries as $battery) {
                VehicleBatterySizeCategoryModel::firstOrCreate([
                    'vehicle_id' => $vehicle->id,
                    'battery_size_category_id' => $battery->id,
                ]);
            }

            $this->totalInsertedRows++;
            return $vehicle;
        } catch (\Exception $e) {
            $this->unimportedRows[] = $row;
            return null;
        }
    }

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
