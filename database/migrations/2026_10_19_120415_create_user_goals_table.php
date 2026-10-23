<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('goal_preset_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_custom')->default(false);
            $table->unsignedInteger('calories');
            $table->decimal('protein', 6, 1);
            $table->decimal('carbs', 6, 1);
            $table->decimal('fats', 6, 1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_goals');
    }
};