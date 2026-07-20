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
        Schema::table('produk_produksi_hasils', function (Blueprint $table) {
            $table->string('status')->default('proses')->after('hpp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produk_produksi_hasils', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
