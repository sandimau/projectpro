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
        Schema::create('freelance_tagihans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('member_id');
            $table->unsignedBigInteger('absensi_id')->nullable();
            $table->date('tanggal');
            $table->integer('nominal_upah');
            $table->string('dibayar')->default('belum'); // sudah, belum
            $table->unsignedBigInteger('penggajian_id')->nullable();
            $table->string('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('member_id')->references('id')->on('members')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('absensi_id')->references('id')->on('absensis')
                ->onUpdate('cascade')->onDelete('set null');
            $table->foreign('penggajian_id')->references('id')->on('penggajians')
                ->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('freelance_tagihans');
    }
};
