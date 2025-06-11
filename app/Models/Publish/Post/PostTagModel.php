<?php

namespace App\Models\Publish\Post;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostTagModel extends Model
{
    use HasFactory;

    protected $table = 'post_tags';

    protected $fillable = [
        'name',
        'slug',
    ];
}
