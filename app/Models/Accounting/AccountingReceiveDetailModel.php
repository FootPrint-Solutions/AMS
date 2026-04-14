<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// Models
use App\Models\Accounting\AccountingReceiveModel;

class AccountingReceiveDetailModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'accounting_receive_details';

    protected $fillable = [
        'cb_receive_id',
        'account_id',
        'account_name',
        'description',
        'total',
    ];

    protected $casts = [
        'total' => 'double',
    ];

    public function receive()
    {
        return $this->belongsTo(AccountingReceiveModel::class, 'cb_receive_id');
    }
}
