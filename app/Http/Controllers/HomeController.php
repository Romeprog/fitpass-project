<?php

namespace App\Http\Controllers;

use App\Models\GoalPreset;
use App\Models\Meal;
use App\Models\UserGoal;
use App\Models\WorkoutSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Everything the "Today at a glance" cards on the home page need, in one call:
     * the member's calorie and protein goals, today's totals, and the dates of
     * their completed workouts (script.js works out the week count and streak
     * from those dates, the same way the workouts and diet pages do).
     *
     * The browser sends its own date (?date=YYYY-MM-DD) so "today" is the member's day.
     */
    public function summary(Request $request): JsonResponse
    {
        $data = $request->validate([
            'date' => ['required', 'date_format:Y-m-d', 'after:2000-01-01', 'before:2100-01-01'],
        ]);

        $userId = $request->user()->id;

        // Goals: the member's saved goals, or the Balanced plan if they have not saved any yet
        $goal = UserGoal::where('user_id', $userId)->first()
            ?? GoalPreset::where('slug', 'balanced')->first();

        $goals = [
            'cal' => $goal ? (int) $goal->calories : 2200,
            'p' => $goal ? (float) $goal->protein : 150.0,
        ];

        // Today's meals
        $meals = Meal::where('user_id', $userId)->where('eaten_on', $data['date'])->get();

        // Completed workouts from the last year (plenty for the week count and the streak)
        $workouts = WorkoutSession::where('user_id', $userId)
            ->where('status', 'completed')
            ->where('workout_date', '>=', now()->subDays(400)->toDateString())
            ->orderByDesc('workout_date')
            ->get(['workout_date'])
            ->map(fn ($s) => ['date' => $s->workout_date->format('Y-m-d')])
            ->values();

        return response()->json([
            'goals' => $goals,
            'today' => [
                'cal' => (int) $meals->sum('calories'),
                'p' => round((float) $meals->sum('protein'), 1),
                'meals' => $meals->count(),
            ],
            'workouts' => $workouts,
        ]);
    }
}
