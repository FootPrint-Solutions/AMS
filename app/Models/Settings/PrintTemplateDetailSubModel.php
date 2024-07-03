<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrintTemplateDetailSubModel extends Model
{
    use HasFactory;

    protected $table = 'work_order_print_template_details_sub';

    protected $fillable = [
        'work_order_print_template_detail_id',
        'step_no',
        'value',
    ];

    public function master()
    {
        return $this->belongsTo(PrintTemplateDetailModel::class, 'work_order_print_template_detail_id');
    }

    public static function getDetails($tipe, $id)
    {
        return self::where('type', $tipe)->where('work_order_print_template_detail_id', $id)->get();
    }

    // connect to detail table
    public function detailssub()
    {
        return $this->hasMany(PrintTemplateDetailSubModel::class, 'work_order_print_template_details_id');
    }
}
