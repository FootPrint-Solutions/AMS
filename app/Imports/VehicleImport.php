<?php

namespace App\Imports;

use App\Models\MasterData\Vehicle\VehicleBrandModel;
use App\Models\MasterData\Vehicle\VehicleModel;


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
        $brand = VehicleBrandModel::firstOrCreate(['name' => trim($row[1])]);

        $vehicle = VehicleModel::create([
            'name' => $row[0],
            'brand_id' => $brand->id,
            'url' => $row[6],
        ]);

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
