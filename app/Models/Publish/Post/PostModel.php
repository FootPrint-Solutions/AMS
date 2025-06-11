<?php

namespace App\Models\Publish\Post;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Publish\Post\PostCategoryModel;
use App\Models\Publish\Post\PostTagModel;
use App\Models\Publish\Post\PostVisit;
use App\Models\User;

class PostModel extends Model
{
    use HasFactory, SoftDeletes;

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
