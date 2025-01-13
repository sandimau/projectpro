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
        Schema::table('order_details', function (Blueprint $table) {
            $table->tinyInteger('bahan')->nullable();
            $table->tinyInteger('kalkir')->nullable();
            $table->tinyInteger('screen')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropColumn('bahan');
            $table->dropColumn('kalkir');
            $table->dropColumn('screen');
        });
    }
};
