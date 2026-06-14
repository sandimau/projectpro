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
        // Kolom status sudah ditambahkan oleh migration 2026_01_17, jadi di sini
        // cukup menambahkan finished_at saja agar tidak duplikat saat migrate:fresh.
        Schema::table('produk_produksi_hasils', function (Blueprint $table) {
            $table->timestamp('finished_at')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produk_produksi_hasils', function (Blueprint $table) {
            $table->dropColumn('finished_at');
        });
    }
};
