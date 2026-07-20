<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('hutang_details')) {
            return;
        }

        Schema::create('hutang_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hutang_id');
            $table->unsignedBigInteger('akun_detail_id');
            $table->date('tanggal');
            $table->integer('jumlah');
            $table->string('keterangan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hutang_details');
    }
};
