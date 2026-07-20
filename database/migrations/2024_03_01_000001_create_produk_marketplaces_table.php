<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('produk_marketplaces')) {
            return;
        }

        Schema::create('produk_marketplaces', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('produk_id');
            $table->integer('marketplace_id');
            $table->bigInteger('item_id');
            $table->bigInteger('model_id');
            $table->integer('paket');
            $table->integer('harga');
            $table->string('nama');
            $table->string('varian');
            $table->integer('harga_mp');
            $table->timestamp('update_harga_terakhir')->nullable();
            $table->integer('dinaikkan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produk_marketplaces');
    }
};
