<?php

namespace App\Models\Publish\Post;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

use App\Traits\DataTablesTrait;
use OwenIt\Auditing\Auditable as AuditableTrait;

use App\Models\Publish\Post\PostCategoryModel;
use App\Models\Publish\Post\PostTagModel;
use App\Models\Publish\Post\PostVisit;
use App\Models\User;

class PostModel extends Model implements Auditable
{
    use HasFactory, SoftDeletes, DataTablesTrait, AuditableTrait;

    protected $table = 'posts';

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'category_id',
        'featured_image',
        'status',
        'published_at',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'deleted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


    /**
     * Get all data for DataTables.
     * 
     * @param \Illuminate\Http\Request $request The POST request obtained (for DataTables configuration).
     * @return array Associative array containing data for DataTables display.
     */
    public static function allForDataTables($request)
    {
        // Set the list of select and search columns.
        $selectColumns = [
            'posts.id',
            'posts.title',
            'posts.slug',
            'posts.excerpt',
            'posts.content',
            'posts.featured_image',
            'posts.status',
            'posts.published_at',
            'post_categories.name as category_name',
            'users.name as creator_name'
        ];
        $searchColumns = ['posts.title', 'posts.slug', 'post_categories.name', 'users.name'];

        // Build the query to obtain all rows with joins.
        $query = self::query();

        if ($request->status !== null) {
            $query->where('posts.status', $request->status);
        }

        $query->select($selectColumns)
            ->join('post_categories', 'posts.category_id', '=', 'post_categories.id')
            ->join('users', 'posts.created_by', '=', 'users.id');

        return self::getAllRows($request, $query, $selectColumns, $searchColumns, [
            'column' => 'posts.updated_at',
            'direction' => 'desc'
        ]);
    }

    // Relationships
    public function category()
    {
        return $this->belongsTo(PostCategoryModel::class, 'category_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function tags()
    {
        return $this->belongsToMany(PostTagModel::class, 'post_tag', 'post_id', 'tag_id');
    }
    public function visits()
    {
        return $this->hasMany(PostVisit::class, 'post_id');
    }
}
