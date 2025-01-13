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
        Schema::create('lemburs', function (Blueprint $table) {
            $table->id();
            $table->integer('tahun')->nullable();
            $table->integer('bulan')->nullable();
            $table->string('keterangan')->nullable();
            $table->integer('jam')->nullable();
            $table->string('dibayar')->nullable();
            $table->unsignedBigInteger('member_id')->nullable();
            $table->foreign('member_id')->references('id')->on('members')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lemburs');
    }
};
