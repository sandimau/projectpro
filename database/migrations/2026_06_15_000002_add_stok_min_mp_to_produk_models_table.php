<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Kolom stok minimal marketplace per produk model (dipakai di halaman
     * Marketplace > Produk untuk batas stok minimal saat sinkron ke marketplace).
     */
    public function up(): void
    {
        if (!Schema::hasTable('produk_models')) {
            return;
        }

        if (!Schema::hasColumn('produk_models', 'stok_min_mp')) {
            Schema::table('produk_models', function (Blueprint $table) {
                $table->integer('stok_min_mp')->nullable()->after('stok');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('produk_models', 'stok_min_mp')) {
            Schema::table('produk_models', function (Blueprint $table) {
                $table->dropColumn('stok_min_mp');
            });
        }
    }
};
