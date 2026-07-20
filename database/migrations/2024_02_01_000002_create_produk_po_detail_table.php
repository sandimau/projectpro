<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('produk_po_detail')) {
            return;
        }

        Schema::create('produk_po_detail', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('po_id')->nullable();
            $table->bigInteger('produk_id')->nullable();
            $table->integer('jumlah')->nullable();
            $table->integer('jumlahKedatangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produk_po_detail');
    }
};
