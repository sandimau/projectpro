<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            if (! Schema::hasColumn('absensis', 'jam_masuk')) {
                $table->string('jam_masuk')->nullable()->after('keterangan');
            }
            if (! Schema::hasColumn('absensis', 'minutes_late')) {
                $table->integer('minutes_late')->nullable()->after('jam_masuk');
            }
        });
    }

    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            if (Schema::hasColumn('absensis', 'minutes_late')) {
                $table->dropColumn('minutes_late');
            }
            if (Schema::hasColumn('absensis', 'jam_masuk')) {
                $table->dropColumn('jam_masuk');
            }
        });
    }
};
