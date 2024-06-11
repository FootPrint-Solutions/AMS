<?php

namespace App\Models\settings;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrintTemplateModel extends Model
{
    use HasFactory;

    protected $table = 'work_order_print_templates';

    protected $fillable = [
        'step_no',
        'message',
    ];
}
