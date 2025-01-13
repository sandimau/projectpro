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
        Schema::create('produk_models', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->nullable();
            $table->integer('harga')->nullable();
            $table->string('satuan')->nullable();
            $table->string('deskripsi')->nullable();
            $table->tinyInteger('jual')->nullable();
            $table->tinyInteger('beli')->nullable();
            $table->tinyInteger('stok')->nullable();
            $table->string('gambar')->nullable();
            $table->unsignedBigInteger('kategori_id')->nullable();
            $table->foreign('kategori_id')->references('id')->on('produk_kategoris')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('kontak_id')->nullable();
            $table->foreign('kontak_id')->references('id')->on('kontaks')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produks');
    }
};
