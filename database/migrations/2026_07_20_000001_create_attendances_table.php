<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['clock_in', 'clock_out']);
            $table->string('status')->default('hadir');
            $table->date('attendance_date');
            $table->time('attendance_time');
            $table->integer('minutes_late')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'attendance_date', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
