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
        Schema::table('project_mp_details', function (Blueprint $table) {
            $table->unsignedBigInteger('produksi_id')->nullable()->after('project_id');
            $table->foreign('produksi_id')->references('id')->on('produksis')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_mp_details', function (Blueprint $table) {
            $table->dropForeign(['produksi_id']);
            $table->dropColumn('produksi_id');
        });
    }
};
