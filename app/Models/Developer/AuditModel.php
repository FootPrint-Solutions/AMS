<?php

namespace App\Models\Developer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// TRAITS
use App\Traits\DataTablesTrait;

class AuditModel extends Model
{
    use HasFactory, DataTablesTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'audits';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = ['user_type', 'user_id', 'event', 'auditable_type', 'auditable_id', 'old_values', 'new_values', 'url', 'ip_address', 'user_agent', 'tags'];

    /**
     * The list of columns in the associated table.
     * 
     * @var array<string>
     */
    private static $selectColumns = ['audits.id', 'audits.user_type', 'audits.user_id', 'audits.event', 'audits.auditable_type', 'audits.auditable_id', 'audits.old_values', 'audits.new_values', 'audits.url', 'audits.ip_address', 'audits.user_agent', 'audits.tags', 'audits.created_at', 'audits.updated_at', 'users.name'];

    /**
     * Get all data for DataTables.
     * 
     * @param \Illuminate\Http\Request $request The POST request obtained (for DataTables configuration).
     * @return array Associative array containing data for DataTables display.
     */
    public static function allForDataTables($request)
    {
        // Build the query to obtain all rows.
        $query = self::query()
            ->join('users', 'audits.user_id', '=', 'users.id')
            ->select(self::$selectColumns);

        return self::getAllRows($request, $query, self::$selectColumns, self::$selectColumns, ['column' => 'audits.updated_at', 'direction' => 'desc']);
    }
}
