<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $table = 'news';

    protected $fillable = [
        'url',
        'desc',
        'title',
        'date',
        'source',
        'sentimen',
        'alasan',
        'sikap',
        'client',
        'category',
        'created_at'
    ];

    public $timestamps = false; // karena tidak ada updated_at di strukturmu
}
