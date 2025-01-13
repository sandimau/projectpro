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
        Schema::create('tunjangans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('member_id')->nullable();
            $table->foreign('member_id')->references('id')->on('members')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('akun_detail_id')->nullable();
            $table->foreign('akun_detail_id')->references('id')->on('akun_details')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->char('ket', 100);
            $table->integer('jumlah')->nullable();
            $table->integer('saldo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tunjangans');
    }
};
