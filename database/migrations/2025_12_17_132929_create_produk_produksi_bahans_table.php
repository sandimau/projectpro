<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('produk_produksi_bahans')) {
            return;
        }

        Schema::create('produk_produksi_bahans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('produk_id');
            $table->unsignedBigInteger('produksi_id');
            $table->unsignedBigInteger('cabang_id')->nullable();
            $table->integer('hpp')->nullable();
            $table->integer('jumlah')->nullable();
            $table->text('keterangan')->nullable();
            $table->unsignedBigInteger('produk_stok_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produk_produksi_bahans');
    }
};
