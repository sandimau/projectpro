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
        Schema::create('link_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('link_page_id')->constrained()->onDelete('cascade');
            $table->string('type')->default('link'); // social, link, header
            $table->string('title');
            $table->string('url')->nullable();
            $table->string('icon')->nullable(); // path icon/gambar
            $table->string('section')->nullable(); // untuk grouping (contoh: "Belanja dengan Cititex", "Official Online Store")
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('link_items');
    }
};

