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
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->foreign('order_id')->references('id')->on('orders')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('produk_id')->nullable();
            $table->foreign('produk_id')->references('id')->on('produks')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('proses_id')->nullable();
            $table->foreign('proses_id')->references('id')->on('proses')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('produksi_id')->nullable();
            $table->foreign('produksi_id')->references('id')->on('produksis')->onUpdate('cascade')->onDelete('cascade');
            $table->char('tema', 50)->nullable();
            $table->integer('jumlah')->nullable();
            $table->integer('harga')->nullable();
            $table->text('keterangan')->nullable();
            $table->date('deathline')->nullable();
            $table->string('gambar')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};
