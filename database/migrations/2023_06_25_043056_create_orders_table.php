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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kontak_id')->nullable();
            $table->foreign('kontak_id')->references('id')->on('kontaks')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->integer('total')->nullable();
            $table->integer('bayar')->nullable();
            $table->integer('diskon')->nullable();
            $table->char('ket_diskon')->nullable();
            $table->char('jasa')->nullable();
            $table->text('keterangan')->nullable();
            $table->integer('ongkir')->nullable();
            $table->char('pengiriman', 10)->nullable();
            $table->char('invoice', 10)->nullable();
            $table->char('jenis_pembayaran', 10)->nullable();
            $table->text('ket_kirim')->nullable();
            $table->date('deathline')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
