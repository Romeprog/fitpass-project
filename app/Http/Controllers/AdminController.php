<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Models\GoalPreset;
use App\Models\User;
use App\Models\UserGoal;
use App\Models\Workout;
use App\Models\WorkoutSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    private const CATEGORIES = ['strength', 'core', 'cardio', 'full'];
    private const LEVELS = ['Beginner', 'Intermediate', 'Advanced'];

    /* ------------------------------------------------------------------
     * READ: everything the admin panel shows, in one call
     * ------------------------------------------------------------------ */

    /**
     * Only member accounts (role = user) are listed; admin accounts are left out.
     * The page works out the stat cards and the report charts from these lists,
     * except "sessions by workout" and "recent activity", which are built here.
     */
    public function data(): JsonResponse
    {
        $members = User::where('role', 'user')->orderBy('id')->get();

        // Per member: number of completed workouts and the date of the latest saved workout
        $sessionStats = WorkoutSession::selectRaw(
            "user_id, SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS completed, MAX(workout_date) AS last_date"
        )->groupBy('user_id')->get()->keyBy('user_id');

        $goals = UserGoal::with('preset')->get()->keyBy('user_id');

        return response()->json([
            'members' => $members->map(function (User $u) use ($sessionStats, $goals) {
                $s = $sessionStats->get($u->id);
                $g = $goals->get($u->id);

                return [
                    'id' => $u->id,
                    'name' => $u->name,
                    'email' => $u->email,
                    'joined' => $u->created_at?->format('Y-m-d'),
                    'workouts' => $s ? (int) $s->completed : 0,
                    'last' => ($s && $s->last_date) ? substr((string) $s->last_date, 0, 10) : null,
                    'status' => $u->status ?: 'active',
                    'goal' => $g ? [
                        'plan' => (! $g->is_custom && $g->preset && $g->preset->slug) ? $g->preset->slug : 'custom',
                        'cal' => (int) $g->calories,
                        'p' => (float) $g->protein,
                        'c' => (float) $g->carbs,
                        'f' => (float) $g->fats,
                    ] : null,
                ];
            })->values(),

            'workouts' => Workout::with('exercises')->orderBy('id')->get()->map(fn (Workout $w) => [
                'id' => $w->id,
                'slug' => $w->slug,
                'title' => $w->name,
                'cat' => $w->category,
                'level' => $w->level,
                'minutes' => (int) $w->minutes,
                'desc' => $w->description ?? '',
                'exercises' => $w->exercises->map(fn ($e) => [
                    'id' => $e->id,
                    'name' => $e->name,
                    'detail' => $e->detail,
                ])->values(),
            ])->values(),

            // Given foods only (no owner). Members' own saved foods are not listed here.
            'foods' => Food::whereNull('user_id')->orderBy('id')->get()->map(fn (Food $f) => [
                'id' => $f->id,
                'name' => $f->name,
                'cal' => (int) $f->calories,
                'p' => (float) $f->protein,
                'c' => (float) $f->carbs,
                'f' => (float) $f->fats,
            ])->values(),

            'plans' => GoalPreset::orderBy('id')->get()->map(fn (GoalPreset $p) => [
                'id' => $p->id,
                'slug' => $p->slug,
                'title' => $p->name,
                'desc' => $p->description ?? '',
                'cal' => (int) $p->calories,
                'p' => (float) $p->protein,
                'c' => (float) $p->carbs,
                'f' => (float) $p->fats,
            ])->values(),

            'sessionsByWorkout' => $this->sessionsByWorkout(),
            'activity' => $this->recentActivity(),
        ]);
    }

    /* ------------------------------------------------------------------
     * MEMBERS: add, suspend / activate, delete, set daily goals
     * ------------------------------------------------------------------ */

    public function createMember(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'max:100'],
        ], [
            'email.unique' => 'That email is already registered.',
        ]);

        // role defaults to "user" and status to "active" in the database
        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'], // hashed by the User model
        ]);

        return response()->json(['ok' => true], 201);
    }

    /**
     * Suspend or activate a member. A suspended member can not log in
     * (see AuthController::login). Members already logged in are not kicked out.
     */
    public function setMemberStatus(Request $request, User $user): JsonResponse
    {
        $this->onlyMembers($user);

        $data = $request->validate([
            'status' => ['required', Rule::in(['active', 'suspended'])],
        ]);

        $user->status = $data['status'];
        $user->save();

        return response()->json(['ok' => true]);
    }

    /**
     * Delete a member. Their meals, goals, saved workouts and saved foods go with them.
     */
    public function deleteMember(User $user): JsonResponse
    {
        $this->onlyMembers($user);

        $user->delete();

        return response()->json(['ok' => true]);
    }

    /**
     * Set a member's daily goals (a given plan or custom numbers).
     * If the numbers do not match the chosen plan, they are saved as "custom".
     */
    public function saveMemberGoals(Request $request, User $user): JsonResponse
    {
        $this->onlyMembers($user);

        $slugs = GoalPreset::whereNotNull('slug')->pluck('slug')->all();

        $data = $request->validate([
            'plan' => ['required', 'string', Rule::in(array_merge(['custom'], $slugs))],
            'calories' => ['required', 'integer', 'min:500', 'max:20000'],
            'protein' => ['required', 'numeric', 'min:0', 'max:2000'],
            'carbs' => ['required', 'numeric', 'min:0', 'max:2000'],
            'fats' => ['required', 'numeric', 'min:0', 'max:2000'],
        ]);

        $preset = $data['plan'] !== 'custom'
            ? GoalPreset::where('slug', $data['plan'])->first()
            : null;

        $matchesPreset = $preset
            && (int) $preset->calories === (int) $data['calories']
            && abs((float) $preset->protein - (float) $data['protein']) < 0.05
            && abs((float) $preset->carbs - (float) $data['carbs']) < 0.05
            && abs((float) $preset->fats - (float) $data['fats']) < 0.05;

        UserGoal::updateOrCreate(
            ['user_id' => $user->id],
            [
                'goal_preset_id' => $matchesPreset ? $preset->id : null,
                'is_custom' => ! $matchesPreset,
                'calories' => $data['calories'],
                'protein' => $data['protein'],
                'carbs' => $data['carbs'],
                'fats' => $data['fats'],
            ]
        );

        return response()->json(['ok' => true]);
    }

    /* ------------------------------------------------------------------
     * WORKOUTS (with their exercises): add, edit, delete
     * ------------------------------------------------------------------ */

    public function createWorkout(Request $request): JsonResponse
    {
        $data = $this->validateWorkout($request);

        DB::transaction(function () use ($data) {
            $workout = Workout::create([
                'name' => $data['title'],
                'slug' => $this->uniqueSlug($data['title'], 'workouts'),
                'category' => $data['cat'],
                'level' => $data['level'],
                'minutes' => $data['minutes'],
                'description' => $data['desc'] ?? null,
            ]);

            $this->replaceExercises($workout, $data['exercises']);
        });

        return response()->json(['ok' => true], 201);
    }

    /**
     * Edit a workout. The slug stays the same so members' saved workouts keep pointing at it.
     */
    public function updateWorkout(Request $request, Workout $workout): JsonResponse
    {
        $data = $this->validateWorkout($request);

        DB::transaction(function () use ($data, $workout) {
            $workout->update([
                'name' => $data['title'],
                'category' => $data['cat'],
                'level' => $data['level'],
                'minutes' => $data['minutes'],
                'description' => $data['desc'] ?? null,
            ]);

            $this->replaceExercises($workout, $data['exercises']);
        });

        return response()->json(['ok' => true]);
    }

    /**
     * Delete a workout. Its exercises and every member's saved sessions of it go too.
     */
    public function deleteWorkout(Workout $workout): JsonResponse
    {
        $workout->delete();

        return response()->json(['ok' => true]);
    }

    /* ------------------------------------------------------------------
     * FOODS (the given foods): add, edit, delete
     * ------------------------------------------------------------------ */

    public function createFood(Request $request): JsonResponse
    {
        $data = $this->validateFood($request);

        Food::create($this->foodValues($data) + ['user_id' => null, 'name' => $data['name']]);

        return response()->json(['ok' => true], 201);
    }

    public function updateFood(Request $request, Food $food): JsonResponse
    {
        $this->onlyGivenFoods($food);

        $data = $this->validateFood($request, $food);

        $food->update($this->foodValues($data) + ['name' => $data['name']]);

        return response()->json(['ok' => true]);
    }

    public function deleteFood(Food $food): JsonResponse
    {
        $this->onlyGivenFoods($food);

        $food->delete();

        return response()->json(['ok' => true]);
    }

    /* ------------------------------------------------------------------
     * NUTRITION PLANS: add, edit, delete
     * ------------------------------------------------------------------ */

    /**
     * New plans get simple numbers for the "stats" box on the diet page and no sample day.
     */
    public function createPlan(Request $request): JsonResponse
    {
        $data = $this->validatePlan($request);

        GoalPreset::create([
            'slug' => $this->uniqueSlug($data['title'], 'goal_presets'),
            'name' => $data['title'],
            'description' => $data['desc'] ?? null,
            'calories' => $data['cal'],
            'protein' => $data['p'],
            'carbs' => $data['c'],
            'fats' => $data['f'],
            'stats' => [
                'Calories' => $data['cal'] . ' kcal',
                'Protein' => $data['p'] . 'g',
                'Carbs' => $data['c'] . 'g',
                'Fats' => $data['f'] . 'g',
            ],
            'sample_meals' => [],
        ]);

        return response()->json(['ok' => true], 201);
    }

    /**
     * Edit a plan's name, description and daily targets. Members keep the numbers
     * they already have. The ranges and sample day of the 9 given plans are not changed.
     */
    public function updatePlan(Request $request, GoalPreset $plan): JsonResponse
    {
        $data = $this->validatePlan($request);

        $plan->update([
            'name' => $data['title'],
            'description' => $data['desc'] ?? null,
            'calories' => $data['cal'],
            'protein' => $data['p'],
            'carbs' => $data['c'],
            'fats' => $data['f'],
        ]);

        return response()->json(['ok' => true]);
    }

    /**
     * Delete a plan. Members who followed it keep their numbers and are marked as custom.
     */
    public function deletePlan(GoalPreset $plan): JsonResponse
    {
        DB::transaction(function () use ($plan) {
            UserGoal::where('goal_preset_id', $plan->id)->update(['is_custom' => true]);
            $plan->delete(); // the database then clears goal_preset_id on those goals
        });

        return response()->json(['ok' => true]);
    }

    /* ------------------------------------------------------------------
     * helpers
     * ------------------------------------------------------------------ */

    private function onlyMembers(User $user): void
    {
        abort_unless($user->role === 'user', 404);
    }

    private function onlyGivenFoods(Food $food): void
    {
        abort_unless($food->user_id === null, 404);
    }

    private function validateWorkout(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:40'],
            'cat' => ['required', Rule::in(self::CATEGORIES)],
            'level' => ['required', Rule::in(self::LEVELS)],
            'minutes' => ['required', 'integer', 'min:5', 'max:180'],
            'desc' => ['nullable', 'string', 'max:140'],
            'exercises' => ['required', 'array', 'min:1', 'max:50'],
            'exercises.*.name' => ['required', 'string', 'max:80'],
            'exercises.*.detail' => ['nullable', 'string', 'max:80'],
        ], [
            'exercises.required' => 'Add at least one exercise.',
            'exercises.min' => 'Add at least one exercise.',
        ]);
    }

    /**
     * Swap a workout's exercises for the new list (kept in the order given).
     * Members' saved workouts store ticks by position, and WorkoutController
     * already copes when the number of exercises changes.
     */
    private function replaceExercises(Workout $workout, array $exercises): void
    {
        $workout->exercises()->delete();

        foreach (array_values($exercises) as $i => $e) {
            $workout->exercises()->create([
                'name' => $e['name'],
                'detail' => trim($e['detail'] ?? '') !== '' ? $e['detail'] : '10 Reps • 3 Sets',
                'sort_order' => $i,
            ]);
        }
    }

    private function validateFood(Request $request, ?Food $food = null): array
    {
        return $request->validate([
            'name' => [
                'required', 'string', 'max:60',
                Rule::unique('foods', 'name')->whereNull('user_id')->ignore($food?->id),
            ],
            'calories' => ['required', 'numeric', 'min:0', 'max:100000'],
            'protein' => ['required', 'numeric', 'min:0', 'max:10000'],
            'carbs' => ['required', 'numeric', 'min:0', 'max:10000'],
            'fats' => ['required', 'numeric', 'min:0', 'max:10000'],
        ], [
            'name.unique' => 'That food is already in the database.',
        ]);
    }

    private function foodValues(array $data): array
    {
        return [
            'calories' => (int) round((float) $data['calories']),
            'protein' => round((float) $data['protein'], 1),
            'carbs' => round((float) $data['carbs'], 1),
            'fats' => round((float) $data['fats'], 1),
        ];
    }

    private function validatePlan(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:40'],
            'desc' => ['nullable', 'string', 'max:120'],
            'cal' => ['required', 'integer', 'min:500', 'max:20000'],
            'p' => ['required', 'numeric', 'min:0', 'max:2000'],
            'c' => ['required', 'numeric', 'min:0', 'max:2000'],
            'f' => ['required', 'numeric', 'min:0', 'max:2000'],
        ], [
            'cal.min' => 'Calories must be at least 500.',
        ]);
    }

    /**
     * A URL-friendly name that is not used yet: "Full Body" -> full-body, then full-body-2, ...
     */
    private function uniqueSlug(string $name, string $table): string
    {
        $base = Str::slug($name) ?: 'item';
        $slug = $base;

        for ($n = 2; DB::table($table)->where('slug', $slug)->exists(); $n++) {
            $slug = $base . '-' . $n;
        }

        return $slug;
    }

    /**
     * Completed sessions per workout, biggest first: [{ l: label, v: count }].
     */
    private function sessionsByWorkout()
    {
        return WorkoutSession::selectRaw('workout_id, COUNT(*) AS total')
            ->where('status', 'completed')
            ->groupBy('workout_id')
            ->with('workout:id,name')
            ->get()
            ->map(fn ($r) => [
                'l' => str_replace(' Workout', '', $r->workout->name ?? 'Deleted workout'),
                'v' => (int) $r->total,
            ])
            ->sortByDesc('v')
            ->values();
    }

    /**
     * The 6 latest events: member sign-ups and finished workouts, newest first.
     */
    private function recentActivity()
    {
        $joined = User::where('role', 'user')
            ->orderByDesc('created_at')->orderByDesc('id')
            ->limit(6)->get()
            ->map(fn (User $u) => [
                'ts' => $u->created_at?->getTimestamp() ?? 0,
                'date' => $u->created_at?->format('Y-m-d'),
                'text' => $u->name . ' joined FitPass',
                'warn' => $u->status === 'suspended',
            ]);

        $finished = WorkoutSession::with(['user:id,name,status', 'workout:id,name'])
            ->where('status', 'completed')
            ->orderByDesc('completed_at')->orderByDesc('id')
            ->limit(6)->get()
            ->filter(fn ($s) => $s->user && $s->workout)
            ->map(fn ($s) => [
                'ts' => ($s->completed_at ?? $s->updated_at)->getTimestamp(),
                'date' => $s->workout_date?->format('Y-m-d') ?? $s->updated_at->format('Y-m-d'),
                'text' => $s->user->name . ' finished ' . $s->workout->name,
                'warn' => $s->user->status === 'suspended',
            ]);

        return $joined->concat($finished)->sortByDesc('ts')->take(6)->values();
    }
}
