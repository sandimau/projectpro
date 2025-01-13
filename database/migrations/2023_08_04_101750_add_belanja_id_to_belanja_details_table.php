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
        Schema::table('belanja_details', function (Blueprint $table) {
            $table->unsignedBigInteger('belanja_id')->nullable();
            $table->foreign('belanja_id')->references('id')->on('belanjas')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('produk_id')->nullable();
            $table->foreign('produk_id')->references('id')->on('produks')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('belanja_details', function (Blueprint $table) {
            $table->dropForeign(['belanja_id']);
            $table->dropColumn('belanja_id');
            $table->dropForeign(['produk_id']);
            $table->dropColumn('produk_id');
        });
    }
};
