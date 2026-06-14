<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('project_mps')) {
            return;
        }

        Schema::create('project_mps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('marketplace_id')->nullable();
            $table->timestamp('deadline')->nullable();
            $table->integer('total')->nullable();
            $table->integer('bersih')->nullable();
            $table->integer('persen')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('konsumen', 100)->nullable();
            $table->string('nota', 100)->nullable();
            $table->tinyInteger('custom')->nullable();
            $table->string('shipping')->nullable();
            $table->char('kota', 100)->nullable();
            $table->char('provinsi', 100)->nullable();
            $table->boolean('retur')->nullable();
            $table->string('retur_status', 10)->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_mps');
    }
};
