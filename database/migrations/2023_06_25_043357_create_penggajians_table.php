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
        Schema::create('penggajians', function (Blueprint $table) {
            $table->id();
            $table->string('bulan')->nullable();
            $table->string('tahun')->nullable();
            $table->integer('jam_lembur')->nullable();
            $table->integer('pokok')->nullable();
            $table->integer('lembur')->nullable();
            $table->integer('kasbon')->nullable();
            $table->integer('bonus')->nullable();
            $table->integer('total')->nullable();
            $table->integer('lama_kerja')->nullable();
            $table->integer('bagian')->nullable();
            $table->integer('performance')->nullable();
            $table->integer('transportasi')->nullable();
            $table->integer('komunikasi')->nullable();
            $table->integer('kehadiran')->nullable();
            $table->integer('jumlah_lain')->nullable();
            $table->string('lain_lain')->nullable();
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
        Schema::dropIfExists('penggajians');
    }
};
