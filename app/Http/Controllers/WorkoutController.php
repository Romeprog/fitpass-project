<?php

namespace App\Http\Controllers;

use App\Models\Workout;
use App\Models\WorkoutSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkoutController extends Controller
{
    /**
     * All workouts with their exercises, keyed by slug.
     * Same shape the old workoutPlans object in script.js had.
     */
    public function catalog(): JsonResponse
    {
        $plans = [];

        foreach (Workout::with('exercises')->orderBy('id')->get() as $workout) {
            $plans[$workout->slug] = [
                'title' => $workout->name,
                'cat' => $workout->category,
                'level' => $workout->level,
                'minutes' => $workout->minutes,
                'desc' => $workout->description ?? '',
                'exercises' => $workout->exercises
                    ->map(fn ($e) => ['name' => $e->name, 'detail' => $e->detail])
                    ->values()
                    ->all(),
            ];
        }

        return response()->json($plans);
    }

    /**
     * The logged-in member's saved workouts (completed and in progress), newest first.
     */
    public function sessions(Request $request): JsonResponse
    {
        $sessions = WorkoutSession::with('workout:id,name,slug')
            ->where('user_id', $request->user()->id)
            ->where('done_count', '>', 0)
            ->orderByDesc('updated_at')
            ->get();

        return response()->json($sessions->map(fn ($s) => $this->present($s))->values());
    }

    /**
     * Create or update a session. Called every time an exercise is ticked (auto-save)
     * and again when the member presses "Finish & Save Workout".
     *
     * - all exercises ticked  -> status "completed"
     * - some ticked           -> status "in_progress" (can be continued later)
     * - none ticked           -> nothing is kept (an existing session is removed)
     */
    public function save(Request $request): JsonResponse
    {
        $data = $request->validate([
            'session_id' => ['nullable', 'integer'],
            'workout' => ['required', 'string', 'exists:workouts,slug'],
            'checks' => ['required', 'array', 'max:100'],
            'checks.*' => ['boolean'],
            'date' => [
                'required',
                'date_format:Y-m-d',
                'after_or_equal:' . now()->subDays(2)->toDateString(),
                'before_or_equal:' . now()->addDay()->toDateString(),
            ],
        ]);

        $userId = $request->user()->id;
        $workout = Workout::with('exercises')->where('slug', $data['workout'])->firstOrFail();
        $total = $workout->exercises->count();

        // One true/false per exercise, in order (also handles exercises added or removed by the admin later)
        $checks = [];
        for ($i = 0; $i < $total; $i++) {
            $checks[] = (bool) ($data['checks'][$i] ?? false);
        }
        $done = count(array_filter($checks));

        $session = null;
        if (! empty($data['session_id'])) {
            $session = WorkoutSession::where('user_id', $userId)
                ->where('workout_id', $workout->id)
                ->find($data['session_id']);
        }

        if ($done === 0) {
            $session?->delete();

            return response()->json(['session' => null]);
        }

        $completed = $done === $total;

        $attributes = [
            'status' => $completed ? 'completed' : 'in_progress',
            'workout_date' => $data['date'],
            'done_count' => $done,
            'total_count' => $total,
            'minutes' => $workout->minutes,
            'checks' => $checks,
            'completed_at' => $completed ? ($session?->completed_at ?? now()) : null,
        ];

        if ($session) {
            $session->update($attributes);
        } else {
            $session = WorkoutSession::create($attributes + [
                'user_id' => $userId,
                'workout_id' => $workout->id,
                'started_at' => now(),
            ]);
        }

        $session->load('workout:id,name,slug');

        return response()->json(['session' => $this->present($session)]);
    }

    /**
     * Delete one saved workout (only the member's own).
     */
    public function destroy(Request $request, WorkoutSession $workoutSession): JsonResponse
    {
        abort_unless($workoutSession->user_id === $request->user()->id, 404);

        $workoutSession->delete();

        return response()->json(['deleted' => true]);
    }

    /**
     * Delete all of the member's saved workouts ("Clear history").
     */
    public function clear(Request $request): JsonResponse
    {
        WorkoutSession::where('user_id', $request->user()->id)->delete();

        return response()->json(['deleted' => true]);
    }

    /**
     * Shape one session the way script.js expects it.
     */
    private function present(WorkoutSession $session): array
    {
        return [
            'id' => $session->id,
            'plan' => $session->workout->slug,
            'title' => $session->workout->name,
            'status' => $session->status,
            'done' => $session->done_count,
            'total' => $session->total_count,
            'minutes' => $session->minutes,
            'date' => $session->workout_date?->format('Y-m-d'),
            'ts' => $session->updated_at->getTimestamp() * 1000,
            'checks' => $session->checks ?? [],
        ];
    }
}
