<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel marketplace_formats menyimpan:
     *  - struktur format file order/keuangan/stok tiap marketplace (sama antar perusahaan)
     *  - kredensial Open Platform Shopee: partnerId, partnerKey, host (BEDA tiap perusahaan)
     *
     * Sebelumnya tabel ini tidak punya migration (berasal dari import SQL dump).
     * Guard hasTable() agar aman dijalankan pada DB lama yang tabelnya sudah ada.
     */
    public function up(): void
    {
        if (Schema::hasTable('marketplace_formats')) {
            return;
        }

        Schema::create('marketplace_formats', function (Blueprint $table) {
            $table->id();
            $table->string('marketplace')->nullable();
            $table->string('jenis', 40)->nullable();
            $table->string('kolom1', 40)->nullable();
            $table->string('kolom2', 40)->nullable();
            $table->string('kolom3', 40)->nullable();
            $table->tinyInteger('nota')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->tinyInteger('tanggal')->nullable();
            $table->tinyInteger('nama')->nullable();
            $table->tinyInteger('sku')->nullable();
            $table->tinyInteger('sku_anak')->nullable();
            $table->tinyInteger('jumlah')->nullable();
            $table->tinyInteger('harga')->nullable();
            $table->tinyInteger('tema')->nullable();
            $table->tinyInteger('saldo')->nullable();
            $table->integer('barisHeader')->nullable();
            $table->string('formatTanggal', 100)->nullable();
            $table->integer('produk')->nullable();
            $table->string('batal', 100)->nullable();
            $table->integer('ongkir')->nullable();
            $table->integer('deathline')->nullable();

            // Kredensial Shopee Open Platform — diisi per perusahaan
            $table->integer('partnerId')->default(0);
            $table->string('partnerKey')->default('');
            $table->string('host')->default('');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketplace_formats');
    }
};
