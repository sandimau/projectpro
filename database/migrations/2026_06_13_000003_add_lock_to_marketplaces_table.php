<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * ShopeeApi::ambilToken() membaca & menulis kolom `lock` (marketplaces.lock)
     * sebagai penanda agar refresh token tidak jalan paralel. Pada DB lama kolom
     * ini belum ada, sehingga auto-refresh token selalu gagal (QueryException).
     *
     * Migration terpisah & guard hasColumn() agar ikut menambal DB lama yang
     * tabel marketplaces-nya sudah terlanjur ada.
     */
    public function up(): void
    {
        if (! Schema::hasTable('marketplaces')) {
            return;
        }

        if (! Schema::hasColumn('marketplaces', 'lock')) {
            Schema::table('marketplaces', function (Blueprint $table) {
                $table->tinyInteger('lock')->default(0)->after('autosinkron_expired');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('marketplaces', 'lock')) {
            Schema::table('marketplaces', function (Blueprint $table) {
                $table->dropColumn('lock');
            });
        }
    }
};
