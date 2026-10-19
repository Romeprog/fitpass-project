<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoalPreset extends Model
{
    protected $fillable = [
        'slug', 'name', 'description', 'calories', 'protein', 'carbs', 'fats',
        'stats', 'sample_meals',
    ];

    protected $casts = [
        'stats' => 'array',
        'sample_meals' => 'array',
    ];
}