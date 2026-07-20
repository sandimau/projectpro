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
        Schema::create('order_jadwals', function (Blueprint $table) {
            $table->id();
            $table->date('deathline')->nullable();
            $table->unsignedBigInteger('order_detail_id')->nullable();
            $table->foreign('order_detail_id')->references('id')->on('order_details')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('produksi_id')->nullable();
            $table->foreign('produksi_id')->references('id')->on('produksis')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_jadwals');
    }
};
