<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('hutangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kontak_id')->constrained('kontaks');
            $table->foreignId('akun_detail_id')->constrained('akun_details');
            $table->date('tanggal');
            $table->decimal('jumlah', 15, 2);
            $table->text('keterangan')->nullable();
            $table->string('jenis')->default('hutang'); // hutang, piutang
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('hutangs');
    }
};
