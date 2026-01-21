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
            $table->enum('status', ['proses', 'finish'])->default('proses')->after('hpp');
            $table->timestamp('finished_at')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produk_produksi_hasils', function (Blueprint $table) {
            $table->dropColumn(['status', 'finished_at']);
        });
    }
};
