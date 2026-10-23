<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Meal extends Model
{
    protected $fillable = [
        'user_id', 'food_id', 'meal_type', 'food_name', 'servings',
        'calories', 'protein', 'carbs', 'fats', 'eaten_on',
    ];

    protected $casts = ['eaten_on' => 'date'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function food(): BelongsTo
    {
        return $this->belongsTo(Food::class);
    }
}