<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('project_mp_details')) {
            return;
        }

        // Kolom produksi_id ditambahkan oleh migration 2026_01_21_000001.
        Schema::create('project_mp_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id')->nullable();
            $table->unsignedBigInteger('produk_id')->nullable();
            $table->string('tema', 100)->nullable();
            $table->integer('jumlah')->nullable();
            $table->integer('harga')->nullable();
            $table->text('keterangan')->nullable();
            $table->unsignedBigInteger('project_flow_id')->nullable();
            $table->integer('process_id')->nullable();
            $table->string('picture')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->string('nota', 30)->nullable();
            $table->integer('hpp')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_mp_details');
    }
};
