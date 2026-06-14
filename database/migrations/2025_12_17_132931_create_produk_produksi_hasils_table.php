<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('produk_produksi_hasils')) {
            return;
        }

        // Kolom status & finished_at ditambahkan oleh migration alter berikutnya
        // (2026_01_17 & 2026_01_20).
        Schema::create('produk_produksi_hasils', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('produk_id')->nullable();
            $table->unsignedBigInteger('produksi_id')->nullable();
            $table->integer('jumlah')->nullable();
            $table->integer('hpp')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produk_produksi_hasils');
    }
};
