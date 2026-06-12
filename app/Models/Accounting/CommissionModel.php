<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommissionModel extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'commission';

    protected $fillable = [
        'commission_number',
        'date',
        'total',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
        'total' => 'float',
    ];

    public static function generateCommissionNumber()
    {
        // Get the latest added code.
        $latestCode = self::withTrashed()
            ->where('commission_number', 'like', 'CM%')
            ->orderByDesc('commission_number')
            ->value('commission_number');

        // Generate the new commission number.
        $year = substr($latestCode, 2, 2);
        $month = substr($latestCode, 4, 2);
        $currentYear = date('y');
        $currentMonth = date('m');

        $newCode = "CM";
        if ($year == $currentYear) {
            if ($month == $currentMonth) {
                // Generate new code with new iteration only.
                $iteration = substr($latestCode, 6);
                $nextIteration = str_pad((int)$iteration + 1, strlen($iteration), '0', STR_PAD_LEFT);
                $newCode .= $year . $month . $nextIteration;
            } else {
                // Generate new code with new month.
                $newCode .= $year . $currentMonth . '00001';
            }
        } else {
            // Generate new code with new year and new month.
            $newCode .= $currentYear . $currentMonth . '00001';
        }
        return $newCode;
    }
}
