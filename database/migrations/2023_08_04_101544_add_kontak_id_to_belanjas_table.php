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
        Schema::table('belanjas', function (Blueprint $table) {
            $table->unsignedBigInteger('kontak_id')->nullable();
            $table->foreign('kontak_id')->references('id')->on('kontaks')->onUpdate('cascade')->onDelete('cascade');
            $table->date('tanggal_beli')->nullable();
            $table->unsignedBigInteger('akun_detail_id')->nullable();
            $table->foreign('akun_detail_id')->references('id')->on('kontaks')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('belanjas', function (Blueprint $table) {
            $table->dropForeign(['kontak_id']);
            $table->dropColumn('kontak_id');
            $table->dropColumn('tanggal_beli');
            $table->dropForeign(['akun_detail_id']);
            $table->dropColumn('akun_detail_id');
        });
    }
};
