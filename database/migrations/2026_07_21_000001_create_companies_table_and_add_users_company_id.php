<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->boolean('is_active')->default(true);
            $table->json('settings')->nullable();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->after('id');
            $table->index('company_id');
        });

        // Email unik per company (bukan global)
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['email']);
            $table->unique(['company_id', 'email']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropUnique(['company_id', 'email']);
            $table->dropIndex(['company_id']);
            $table->dropColumn('company_id');
            $table->unique('email');
        });

        Schema::dropIfExists('companies');
    }
};
