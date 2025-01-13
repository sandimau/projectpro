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
        Schema::create('buku_besars', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('akun_detail_id')->nullable();
            $table->foreign('akun_detail_id')->references('id')->on('akun_details')->onUpdate('cascade')->onDelete('cascade');
            $table->char('kode',10)->nullable();
            $table->char('ket')->nullable();
            $table->integer('debet')->nullable();
            $table->integer('kredit')->nullable();
            $table->integer('saldo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buku_besars');
    }
};
