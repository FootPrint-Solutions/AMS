<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Accounting\BillingInvoiceModel;

class BillingModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'billings';

    protected $fillable = [
        'billing_number',
        'vendor_id',
        'vendor_type',
        'ship_to_id',
        'ship_to_type',
        'date',
        'discount',
        'discount_price',
        'subtotal',
        'total',
    ];

    protected $dates = [
        'date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public static function generateBillingNumber()
    {
        $latestCodeModel = self::withTrashed()
            ->orderByDesc('created_at')
            ->first();
        $latestCode = $latestCodeModel ? $latestCodeModel->billing_number : null;

        $year = substr($latestCode, 2, 2);
        $month = substr($latestCode, 4, 2);
        $currentYear = date('y');
        $currentMonth = date('m');

        $newCode = "AB";
        if ($year == $currentYear) {
            if ($month == $currentMonth) {
                $iteration = substr($latestCode, 6);
                $nextIteration = str_pad((int)$iteration + 1, strlen($iteration), '0', STR_PAD_LEFT);
                $newCode .= $year . $month . $nextIteration;
            } else {
                $newCode .= $year . $currentMonth . '00001';
            }
        } else {
            $newCode .= $currentYear . $currentMonth . '00001';
        }
        return $newCode;
    }

    public static function generateSalesBillingNumber()
    {
        $latestCodeModel = self::withTrashed()
            ->orderByDesc('created_at')
            ->first();
        $latestCode = $latestCodeModel ? $latestCodeModel->billing_number : null;

        $year = substr($latestCode, 2, 2);
        $month = substr($latestCode, 4, 2);
        $currentYear = date('y');
        $currentMonth = date('m');

        $newCode = "SB";
        if ($year == $currentYear) {
            if ($month == $currentMonth) {
                $iteration = substr($latestCode, 6);
                $nextIteration = str_pad((int)$iteration + 1, strlen($iteration), '0', STR_PAD_LEFT);
                $newCode .= $year . $month . $nextIteration;
            } else {
                $newCode .= $year . $currentMonth . '00001';
            }
        } else {
            $newCode .= $currentYear . $currentMonth . '00001';
        }
        return $newCode;
    }

    public static function generatePurchaseBillingNumber()
    {
        $latestCodeModel = self::withTrashed()
            ->orderByDesc('created_at')
            ->first();
        $latestCode = $latestCodeModel ? $latestCodeModel->billing_number : null;

        $year = substr($latestCode, 2, 2);
        $month = substr($latestCode, 4, 2);
        $currentYear = date('y');
        $currentMonth = date('m');

        $newCode = "PB";
        if ($year == $currentYear) {
            if ($month == $currentMonth) {
                $iteration = substr($latestCode, 6);
                $nextIteration = str_pad((int)$iteration + 1, strlen($iteration), '0', STR_PAD_LEFT);
                $newCode .= $year . $month . $nextIteration;
            } else {
                $newCode .= $year . $currentMonth . '00001';
            }
        } else {
            $newCode .= $currentYear . $currentMonth . '00001';
        }
        return $newCode;
    }

    public function vendor()
    {
        return $this->morphTo();
    }

    public function shipTo()
    {
        return $this->morphTo();
    }

    public function invoices()
    {
        return $this->hasMany(BillingInvoiceModel::class, 'billing_id')->with('invoice');
    }
}
