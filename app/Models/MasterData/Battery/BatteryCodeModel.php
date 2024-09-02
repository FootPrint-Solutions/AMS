<?php

namespace App\Models\MasterData\Battery;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BatteryCodeModel extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'battery_codes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'battery_id',
        'code'
    ];

    /**
     * Get battery.
     */
    public function code(): BelongsTo
    {
        return $this->belongsTo(BatteryModel::class);
    }

    /**
     * Generates a new battery code based on the current date and the last generated code.
     *
     * @return string The generated battery code.
     */
    public static function generateCode()
    {
        $lastCode = self::where('code', 'like', date('Y-m-d') . '%')->orderBy('code', 'desc')->first();
        if ($lastCode) {
            $lastCode = $lastCode->code;
            $date = substr($lastCode, 0, 10);
            $date = str_replace('-', '', $date);
            $number = substr($lastCode, 10);
            $number = (int)$number + 1;
            $number = str_pad($number, 3, '0', STR_PAD_LEFT);
            return str_replace("-", "", $date . $number);
        } else {
            return str_replace("-", "", date('Y-m-d') . '001');
        }
    }
}
