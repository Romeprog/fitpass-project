<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('goal_presets', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->nullable()->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedInteger('calories');
            $table->decimal('protein', 6, 1);
            $table->decimal('carbs', 6, 1);
            $table->decimal('fats', 6, 1);
            $table->json('stats')->nullable();        // {"Calories":"2800-3200", ...}
            $table->json('sample_meals')->nullable(); // list of sample day lines
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('goal_presets');
    }
};