<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('produksi_produks')) {
            return;
        }

        Schema::create('produksi_produks', function (Blueprint $table) {
            $table->id();
            $table->char('status', 20);
            $table->integer('biaya')->nullable();
            $table->integer('hasil')->nullable();
            $table->integer('target')->nullable();
            $table->integer('hpp')->nullable();
            $table->unsignedBigInteger('produk_id');
            $table->unsignedBigInteger('cabang_id');
            $table->string('ket')->nullable();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produksi_produks');
    }
};
