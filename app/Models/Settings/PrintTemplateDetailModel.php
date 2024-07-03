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

    /**
     * Get all data for DataTables.
     * 
     * @param \Illuminate\Http\Request $request The POST request obtained (for DataTables configuration).
     * @return array Associative array containing data for DataTables display.
     */
    public function master()
    {
        return $this->belongsTo(PrintTemplateModel::class, 'work_order_print_template_master_id');
    }

    public static function getDetails($tipe, $id)
    {
        return self::where('type', $tipe)->where('work_order_print_template_master_id', $id)->get();
    }

    // connect to detail table
    public function detailssub()
    {
        return $this->hasMany(PrintTemplateDetailSubModel::class, 'work_order_print_template_details_id');
    }

    //subDetails
    public function subDetails()
    {
        return $this->hasMany(PrintTemplateDetailSubModel::class, 'work_order_print_template_details_id');
    }
}
