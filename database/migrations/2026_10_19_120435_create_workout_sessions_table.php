<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workout_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('workout_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('in_progress'); // in_progress or completed
            $table->date('workout_date')->nullable();
            $table->unsignedSmallInteger('done_count')->default(0);
            $table->unsignedSmallInteger('total_count')->default(0);
            $table->unsignedSmallInteger('minutes')->default(0);
            $table->json('checks')->nullable(); // [true,false,true,...] in exercise order
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workout_sessions');
    }
};