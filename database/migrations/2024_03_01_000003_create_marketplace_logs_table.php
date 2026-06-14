<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('marketplace_logs')) {
            return;
        }

        Schema::create('marketplace_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->text('isi');
            $table->string('jenis');
            $table->char('marketplace', 30);
            $table->integer('company_id');
            $table->integer('shop_id');
            $table->timestamp('tanggal')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketplace_logs');
    }
};
