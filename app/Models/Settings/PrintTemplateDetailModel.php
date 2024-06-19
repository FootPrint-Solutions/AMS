<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrintTemplateDetailModel extends Model
{
    use HasFactory;

    protected $table = 'work_order_print_template_details';

    protected $fillable = [
        'work_order_print_template_master_id',
        'step_no',
        'type',
        'message',
    ];
}
