<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('produk_po')) {
            return;
        }

        Schema::create('produk_po', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('kontak_id')->nullable();
            $table->date('tglKedatangan')->nullable();
            $table->string('status', 100)->nullable();
            $table->text('ket')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produk_po');
    }
};
