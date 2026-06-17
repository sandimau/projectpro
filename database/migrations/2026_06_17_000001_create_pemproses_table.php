<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('pemproses')) {
            return;
        }

        Schema::create('pemproses', function (Blueprint $table) {
            $table->id();
            $table->char('nama', 50);
            $table->char('warna', 10)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemproses');
    }
};
