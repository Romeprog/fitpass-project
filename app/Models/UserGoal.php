<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserGoal extends Model
{
    protected $fillable = ['user_id', 'goal_preset_id', 'is_custom', 'calories', 'protein', 'carbs', 'fats'];

    protected $casts = ['is_custom' => 'boolean'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function preset(): BelongsTo
    {
        return $this->belongsTo(GoalPreset::class, 'goal_preset_id');
    }
}