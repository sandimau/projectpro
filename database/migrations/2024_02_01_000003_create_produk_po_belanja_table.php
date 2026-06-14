<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('produk_po_belanja')) {
            return;
        }

        Schema::create('produk_po_belanja', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('po_id');
            $table->unsignedBigInteger('belanja_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produk_po_belanja');
    }
};
