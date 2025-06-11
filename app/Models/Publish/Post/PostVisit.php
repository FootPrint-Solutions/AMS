<?php

namespace App\Models\Publish\Post;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Publish\Post\PostModel;

class PostVisit extends Model
{
    use HasFactory;

    protected $table = 'post_visits';

    protected $fillable = [
        'post_id',
        'ip_address',
        'country',
        'region',
        'city',
        'browser',
        'platform',
        'device',
        'user_agent',
    ];

    public function post()
    {
        return $this->belongsTo(PostModel::class, 'post_id');
    }
}
