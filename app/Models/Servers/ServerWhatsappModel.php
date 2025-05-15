<?php

namespace App\Models\Servers;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServerWhatsappModel extends Model
{
    use HasFactory;

    protected $table = 'server_whatsapp';

    protected $fillable = [
        'name',
        'number',
        'url',
    ];
}
