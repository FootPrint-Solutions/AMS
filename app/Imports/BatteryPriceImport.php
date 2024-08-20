<?php

namespace App\Imports;

use App\Models\MasterData\Battery\BatteryModel;
use App\Models\MasterData\Battery\BatteryPriceModel;
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
        $battery = BatteryModel::where('name', $row[0])->first();
        if ($battery)
            $battery->update([
                'price_retail' => $row[13] != '-' ? $row[13] : null,
            ]);

        return $battery;
    }
}
