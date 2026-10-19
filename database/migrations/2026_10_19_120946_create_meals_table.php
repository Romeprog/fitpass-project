<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('food_id')->nullable()->constrained('foods')->nullOnDelete();
            $table->string('meal_type'); // breakfast, lunch, dinner, snack
            $table->string('food_name');
            $table->decimal('servings', 5, 2)->default(1);
            $table->unsignedInteger('calories');
            $table->decimal('protein', 6, 1)->default(0);
            $table->decimal('carbs', 6, 1)->default(0);
            $table->decimal('fats', 6, 1)->default(0);
            $table->date('eaten_on');
            $table->timestamps();

            $table->index(['user_id', 'eaten_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meals');
    }
};