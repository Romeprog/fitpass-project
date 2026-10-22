<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Models\GoalPreset;
use App\Models\Meal;
use App\Models\UserGoal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DietController extends Controller
{
    private const MEAL_TYPES = ['breakfast', 'lunch', 'dinner', 'snack'];

    /**
     * Everything the diet page needs when it opens:
     * the 9 given plans (keyed by slug), the member's goals, and the foods for quick-add.
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $plans = [];
        foreach (GoalPreset::orderBy('id')->get() as $preset) {
            if (! $preset->slug) {
                continue;
            }

            $plans[$preset->slug] = [
                'title' => $preset->name,
                'desc' => $preset->description ?? '',
                'stats' => $preset->stats ?? [],
                'meals' => $preset->sample_meals ?? [],
                'goal' => [
                    'cal' => (int) $preset->calories,
                    'p' => (float) $preset->protein,
                    'c' => (float) $preset->carbs,
                    'f' => (float) $preset->fats,
                ],
            ];
        }

        return response()->json([
            'plans' => $plans,
            'goals' => $this->currentGoals($userId),
            'foods' => [
                'given' => Food::whereNull('user_id')->orderBy('id')->get()
                    ->map(fn ($f) => $this->presentFood($f))->values(),
                'mine' => Food::where('user_id', $userId)->orderBy('id')->get()
                    ->map(fn ($f) => $this->presentFood($f))->values(),
            ],
        ]);
    }

    /**
     * Save the member's daily goals (a given plan, or custom numbers).
     * If the numbers do not match the chosen plan, the goals are saved as "custom".
     */
    public function saveGoals(Request $request): JsonResponse
    {
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

        $goal = UserGoal::updateOrCreate(
            ['user_id' => $request->user()->id],
            [
                'goal_preset_id' => $matchesPreset ? $preset->id : null,
                'is_custom' => ! $matchesPreset,
                'calories' => $data['calories'],
                'protein' => $data['protein'],
                'carbs' => $data['carbs'],
                'fats' => $data['fats'],
            ]
        );

        return response()->json([
            'goals' => $this->goalArray(
                $matchesPreset ? $preset->slug : 'custom',
                $goal->calories,
                $goal->protein,
                $goal->carbs,
                $goal->fats
            ),
        ]);
    }

    /**
     * The member's meals between two dates (inclusive), oldest first.
     * The diet page asks for 7 days at a time (the day list + the 7-day chart).
     */
    public function meals(Request $request): JsonResponse
    {
        $data = $request->validate([
            'from' => ['required', 'date_format:Y-m-d'],
            'to' => ['required', 'date_format:Y-m-d', 'after_or_equal:from'],
        ]);

        $days = abs(\Carbon\Carbon::parse($data['from'])->diffInDays(\Carbon\Carbon::parse($data['to'])));
        abort_if($days > 31, 422, 'Please ask for 31 days or fewer.');

        $meals = Meal::where('user_id', $request->user()->id)
            ->whereBetween('eaten_on', [$data['from'], $data['to']])
            ->orderBy('id')
            ->get();

        return response()->json($meals->map(fn ($m) => $this->presentMeal($m))->values());
    }

    /**
     * Add one meal entry. Optionally also save it to the member's own foods (per single serving).
     */
    public function addMeal(Request $request): JsonResponse
    {
        $data = $request->validate([
            'date' => ['required', 'date_format:Y-m-d', 'after:2000-01-01', 'before:2100-01-01'],
            'meal' => ['required', Rule::in(self::MEAL_TYPES)],
            'name' => ['required', 'string', 'max:60'],
            'servings' => ['required', 'numeric', 'min:0.01', 'max:99'],
            'calories' => ['required', 'numeric', 'min:0', 'max:100000'],
            'protein' => ['nullable', 'numeric', 'min:0', 'max:10000'],
            'carbs' => ['nullable', 'numeric', 'min:0', 'max:10000'],
            'fats' => ['nullable', 'numeric', 'min:0', 'max:10000'],
            'food_id' => ['nullable', 'integer'],
            'save_food' => ['nullable', 'boolean'],
        ]);

        $userId = $request->user()->id;

        // Only link to a food the member is allowed to use (a given food or one of their own)
        $foodId = null;
        if (! empty($data['food_id'])) {
            $foodId = Food::where('id', $data['food_id'])
                ->where(fn ($q) => $q->whereNull('user_id')->orWhere('user_id', $userId))
                ->value('id');
        }

        $calories = (float) $data['calories'];
        $protein = (float) ($data['protein'] ?? 0);
        $carbs = (float) ($data['carbs'] ?? 0);
        $fats = (float) ($data['fats'] ?? 0);
        $servings = (float) $data['servings'];

        $meal = Meal::create([
            'user_id' => $userId,
            'food_id' => $foodId,
            'meal_type' => $data['meal'],
            'food_name' => $data['name'],
            'servings' => $servings,
            'calories' => (int) round($calories),
            'protein' => round($protein, 1),
            'carbs' => round($carbs, 1),
            'fats' => round($fats, 1),
            'eaten_on' => $data['date'],
        ]);

        $food = null;
        if ($request->boolean('save_food')) {
            // Save per single serving, and drop any "× N" that quick add put in the name
            $base = trim(preg_replace('/\s*×\s*[\d.]+$/u', '', $data['name']));
            $base = $base !== '' ? $base : $data['name'];

            $values = [
                'calories' => (int) round($calories / $servings),
                'protein' => round($protein / $servings, 1),
                'carbs' => round($carbs / $servings, 1),
                'fats' => round($fats / $servings, 1),
            ];

            $food = Food::where('user_id', $userId)
                ->whereRaw('LOWER(name) = ?', [mb_strtolower($base)])
                ->first();

            if ($food) {
                $food->update($values);
            } else {
                $food = Food::create($values + ['user_id' => $userId, 'name' => $base]);
            }
        }

        return response()->json([
            'meal' => $this->presentMeal($meal),
            'food' => $food ? $this->presentFood($food) : null,
        ], 201);
    }

    /**
     * Delete one meal entry (only the member's own).
     */
    public function deleteMeal(Request $request, Meal $meal): JsonResponse
    {
        abort_unless((int) $meal->user_id === (int) $request->user()->id, 404);

        $meal->delete();

        return response()->json(['deleted' => true]);
    }

    /**
     * Remove one of the member's own saved foods. Given foods (no owner) can not be removed here.
     */
    public function deleteFood(Request $request, Food $food): JsonResponse
    {
        abort_unless(
            $food->user_id !== null && (int) $food->user_id === (int) $request->user()->id,
            404
        );

        $food->delete();

        return response()->json(['deleted' => true]);
    }

    /**
     * The member's saved goals, or the Balanced plan if they have not saved any yet.
     */
    private function currentGoals(int $userId): array
    {
        $goal = UserGoal::with('preset')->where('user_id', $userId)->first();

        if ($goal) {
            $plan = (! $goal->is_custom && $goal->preset && $goal->preset->slug)
                ? $goal->preset->slug
                : 'custom';

            return $this->goalArray($plan, $goal->calories, $goal->protein, $goal->carbs, $goal->fats);
        }

        $balanced = GoalPreset::where('slug', 'balanced')->first();

        return $balanced
            ? $this->goalArray('balanced', $balanced->calories, $balanced->protein, $balanced->carbs, $balanced->fats)
            : $this->goalArray('custom', 2200, 150, 265, 70);
    }

    /**
     * Goals in the short shape script.js uses: { plan, cal, p, c, f }.
     */
    private function goalArray(string $plan, $calories, $protein, $carbs, $fats): array
    {
        return [
            'plan' => $plan,
            'cal' => (int) $calories,
            'p' => (float) $protein,
            'c' => (float) $carbs,
            'f' => (float) $fats,
        ];
    }

    private function presentFood(Food $food): array
    {
        return [
            'id' => $food->id,
            'name' => $food->name,
            'cal' => (int) $food->calories,
            'p' => (float) $food->protein,
            'c' => (float) $food->carbs,
            'f' => (float) $food->fats,
        ];
    }

    private function presentMeal(Meal $meal): array
    {
        return [
            'id' => $meal->id,
            'date' => $meal->eaten_on->format('Y-m-d'),
            'meal' => $meal->meal_type,
            'name' => $meal->food_name,
            'servings' => (float) $meal->servings,
            'cal' => (int) $meal->calories,
            'p' => (float) $meal->protein,
            'c' => (float) $meal->carbs,
            'f' => (float) $meal->fats,
        ];
    }
}
