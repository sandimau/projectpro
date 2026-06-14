<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('marketplace_buffers')) {
            return;
        }

        Schema::create('marketplace_buffers', function (Blueprint $table) {
            $table->id();
            $table->string('nota')->nullable()->unique('idx_nota_unique');
            $table->string('shop_id')->nullable();
            $table->string('status')->nullable();
            $table->bigInteger('project_id')->nullable();
            $table->bigInteger('marketplace_id')->nullable();
            $table->boolean('custom')->nullable();
            $table->string('mp', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketplace_buffers');
    }
};
