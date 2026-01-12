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
        Schema::create('link_pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique(); // contoh: bandung, jakarta
            $table->string('title'); // contoh: Cititex Bandung
            $table->string('logo')->nullable(); // gambar logo
            $table->string('background_color')->default('#000000');
            $table->string('text_color')->default('#ffffff');
            $table->string('button_color')->default('#ffffff');
            $table->string('button_text_color')->default('#000000');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('link_pages');
    }
};

