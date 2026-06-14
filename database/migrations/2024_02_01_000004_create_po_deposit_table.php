<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('po_deposit')) {
            return;
        }

        Schema::create('po_deposit', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('po_id');
            $table->unsignedBigInteger('hutang_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('po_deposit');
    }
};
