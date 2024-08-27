<?php

namespace App\Imports;

use App\Models\MasterData\Battery\BatteryCodeModel;
use App\Models\MasterData\Battery\BatteryModel;
use App\Models\MasterData\Battery\BatteryPriceModel;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class BatteryPriceImport implements ToModel, WithStartRow
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
     * @return Battery|null
     */
    public function model(array $row)
    {
        // Get new values (to replace).
        $newName = $row[1] ? $row[1] : "";
        $newPrice = $row[14] ? intval($row[14]) : 0;

        // Get battery based on code.
        $code = $row[0];
        $batteryId = BatteryCodeModel::where('code', $code)->first();
        if (!$batteryId)
            return;
        $batteryId = $batteryId->battery_id;

        $battery = BatteryModel::where('id', $batteryId)->first();
        if (!$battery)
            return;

        $battery->name = $newName;
        $battery->price_retail = $newPrice;
        $battery->save();
        return $battery;
    }
}
