<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewsTable extends Migration
{
    public function up()
    {
        Schema::create('news', function (Blueprint $table) {
            $table->id();                     // Kolom ID
            $table->timestamps();             // Kolom created_at dan updated_at
            $table->string('url');             // URL berita
            $table->text('desc')->nullable(); // Deskripsi berita
            $table->string('title');           // Judul berita
            $table->date('date');              // Tanggal berita
            $table->string('source')->nullable(); // Sumber berita
            $table->string('category')->nullable(); // Kategori berita
            $table->string('sentiment')->nullable(); // Sentimen berita
            $table->text('alasan')->nullable(); // Alasan untuk sentimen
        });
    }

    public function down()
    {
        Schema::dropIfExists('news'); // Drop tabel jika rollback
    }
}

