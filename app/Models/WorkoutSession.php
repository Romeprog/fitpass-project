<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkoutSession extends Model
{
    protected $fillable = [
        'user_id', 'workout_id', 'status', 'workout_date',
        'done_count', 'total_count', 'minutes', 'checks',
        'started_at', 'completed_at',
    ];

    protected $casts = [
        'checks' => 'array',
        'workout_date' => 'date',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function workout(): BelongsTo
    {
        return $this->belongsTo(Workout::class);
    }
}