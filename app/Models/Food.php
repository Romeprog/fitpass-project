<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Food extends Model
{
    protected $table = 'foods';

    protected $fillable = ['user_id', 'name', 'calories', 'protein', 'carbs', 'fats'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}