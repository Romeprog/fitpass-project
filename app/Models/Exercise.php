<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Exercise extends Model
{
    protected $fillable = ['workout_id', 'name', 'detail', 'description', 'sort_order'];

    public function workout(): BelongsTo
    {
        return $this->belongsTo(Workout::class);
    }
}