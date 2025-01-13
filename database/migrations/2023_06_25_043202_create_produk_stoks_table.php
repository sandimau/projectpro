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
        Schema::create('produk_stoks', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->nullable();
            $table->integer('tambah')->nullable();
            $table->integer('kurang')->nullable();
            $table->integer('saldo')->nullable();
            $table->string('keterangan')->nullable();
            $table->string('kode')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produk_stoks');
    }
};
