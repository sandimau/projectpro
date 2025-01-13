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
        Schema::table('penggajians', function (Blueprint $table) {
            $table->unsignedBigInteger('akun_detail_id')->nullable();
            $table->foreign('akun_detail_id')->references('id')->on('akun_details')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penggajians', function (Blueprint $table) {
            $table->dropForeign(['akun_detail_id']);
            $table->dropColumn('akun_detail_id');
        });
    }
};
