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
        Schema::create('order_speks', function (Blueprint $table) {
            $table->id();
            $table->string('keterangan')->nullable();
            $table->unsignedBigInteger('spek_id')->nullable();
            $table->foreign('spek_id')->references('id')->on('speks')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('order_detail_id')->nullable();
            $table->foreign('order_detail_id')->references('id')->on('order_details')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_speks');
    }
};
