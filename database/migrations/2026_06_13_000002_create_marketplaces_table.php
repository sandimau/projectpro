<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel marketplaces menyimpan tiap toko/akun marketplace yang terhubung
     * (shop_id, access_token, refresh_token, dst). Data ini DIISI lewat aplikasi
     * (otorisasi Shopee), bukan di-seed.
     *
     * Sebelumnya tabel ini tidak punya migration (berasal dari import SQL dump).
     * Guard hasTable() agar aman dijalankan pada DB lama yang tabelnya sudah ada.
     */
    public function up(): void
    {
        if (Schema::hasTable('marketplaces')) {
            return;
        }

        Schema::create('marketplaces', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->nullable();
            $table->string('marketplace', 100)->nullable();
            $table->unsignedBigInteger('kontak_id')->nullable();
            $table->unsignedBigInteger('kas_id')->nullable();
            $table->unsignedBigInteger('penarikan_id')->nullable();
            $table->tinyInteger('baruOrder')->nullable();
            $table->tinyInteger('baruKeuangan')->nullable();
            $table->timestamp('tglUploadKeuangan')->nullable();
            $table->timestamp('tglUploadOrder')->nullable();
            $table->string('warna', 100)->nullable();
            $table->timestamps();
            $table->timestamp('tglUploadStok')->nullable();
            $table->bigInteger('iklan')->nullable();
            $table->unsignedBigInteger('shop_id')->default(0);
            $table->string('access_token')->nullable();
            $table->string('refresh_token')->nullable();
            $table->string('autosinkron')->nullable();
            $table->integer('access_expired')->nullable();
            $table->timestamp('autosinkron_expired')->nullable();

            // Dipakai ShopeeApi::ambilToken() sebagai lock saat refresh token
            $table->tinyInteger('lock')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketplaces');
    }
};
