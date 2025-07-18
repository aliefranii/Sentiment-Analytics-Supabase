<?php
// app/Models/News.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;

    // Menonaktifkan timestamps (created_at dan updated_at)
    public $timestamps = false;

    // Nama tabel yang digunakan oleh model ini
    protected $table = 'news';

    // Kolom yang dapat diisi
    protected $fillable = [
        'url',
        'desc',
        'title',
        'date',
        'source',
        'category',
        'sentiment',
        'alasan',
    ];
}
