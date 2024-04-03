<?php

namespace App\Imports;

use App\Models\MasterData\Vehicle\VehicleBrandModel;
use App\Models\MasterData\Vehicle\VehicleModel;
use App\Models\MasterData\Battery\BatteryModel;
use App\Models\MasterData\Vehicle\VehicleBatteryModel;


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
        $primarybattery = BatteryModel::where('name_alternate', $row[2])->firstOr(function () {
            return '';
        });

        $altbattery1 = BatteryModel::where('name_alternate', $row[3])->firstOr(function () {
            return '';
        });

        $altbattery2 = BatteryModel::where('name_alternate', $row[4])->firstOr(function () {
            return '';
        });

        $altbattery3 = BatteryModel::where('name_alternate', $row[5])->firstOr(function () {
            return '';
        });

        $vehicle = VehicleModel::create([
            'name' => $row[0],
            'brand_id' => $brand->id,
            'url' => $row[6],
        ]);

        if ($primarybattery != '') {
            VehicleBatteryModel::create([
                'vehicle_id' => $vehicle->id,
                'battery_id' => $primarybattery->id,
                'type' => "0",
            ]);
        }

        if ($altbattery1 != '') {
            VehicleBatteryModel::create([
                'vehicle_id' => $vehicle->id,
                'battery_id' => $altbattery1->id,
                'type' => "1",
            ]);
        }

        if ($altbattery2 != '') {
            VehicleBatteryModel::create([
                'vehicle_id' => $vehicle->id,
                'battery_id' => $altbattery2->id,
                'type' => "1",
            ]);
        }

        if ($altbattery3 != '') {
            VehicleBatteryModel::create([
                'vehicle_id' => $vehicle->id,
                'battery_id' => $altbattery3->id,
                'type' => "1",
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
