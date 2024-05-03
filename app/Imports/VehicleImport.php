<?php

namespace App\Imports;

use App\Models\MasterData\Vehicle\VehicleBrandModel;
use App\Models\MasterData\Vehicle\VehicleModel;
use App\Models\MasterData\Battery\BatteryModel;
use App\Models\MasterData\Battery\BatterySizeCategoryModel;
use App\Models\MasterData\Vehicle\VehicleBatteryModel;
use App\Models\MasterData\Vehicle\VehicleBatterySizeCategoryModel;


use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class VehicleImport implements ToModel, WithStartRow
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
        if (empty($row[0])) {
            return null;
        }

        $brand = VehicleBrandModel::firstOrCreate(['name' => trim($row[1])]);

        $altbatteries = [];
        for ($i = 2; $i <= 5; $i++) {
            if (!empty($row[$i])) {
                $battery = BatterySizeCategoryModel::firstOrCreate(['name' => $row[$i]]);
                $altbatteries[] = $battery;
            }
        }

        $vehicle = VehicleModel::firstOrCreate(
            ['name' => $row[0]], // cari berdasarkan nama ini dulu
            ['brand_id' => $brand->id, 'url' => $row[6]]
        );

        foreach ($altbatteries as $battery) {
            VehicleBatterySizeCategoryModel::firstOrCreate([
                'vehicle_id' => $vehicle->id,
                'battery_size_category_id' => $battery->id,
            ]);
        }

        return $vehicle;
    }

    private function validateRow(array $row): bool
    {
        if (empty($row[1]) || empty($row[4]) || empty($row[5]) || empty($row[6])) {
            return false; // Validation failed
        }

        return true;
    }
}
