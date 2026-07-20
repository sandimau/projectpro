<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('hutangs')) {
            return;
        }

        Schema::create('hutangs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kontak_id')->nullable();
            $table->date('tanggal')->nullable();
            $table->integer('jumlah')->nullable();
            $table->string('keterangan')->nullable();
            $table->string('jenis', 100)->nullable();
            $table->unsignedBigInteger('akun_detail_id')->nullable();
            $table->bigInteger('detail_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hutangs');
    }
};
