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
        Schema::create('produk_pakais', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('produk_id')->nullable();
            $table->foreign('produk_id')->references('id')->on('produks')->onUpdate('cascade')->onDelete('cascade');
            $table->integer('jumlah')->nullable();
            $table->text('keterangan')->nullable();
            $table->unsignedBigInteger('cabang_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->integer('hpp')->nullable();
            $table->unsignedBigInteger('produk_stok_id')->nullable();
            $table->foreign('produk_stok_id')->references('id')->on('produk_stoks')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produk_pakais');
    }
};
