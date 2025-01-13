<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('produk_kategori_utamas', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->nullable();
            $table->tinyInteger('jual')->nullable();
            $table->tinyInteger('beli')->nullable();
            $table->tinyInteger('stok')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kategori_utamas');
    }
};
