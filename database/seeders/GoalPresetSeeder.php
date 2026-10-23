<?php

namespace Database\Seeders;

use App\Models\GoalPreset;
use Illuminate\Database\Seeder;

class GoalPresetSeeder extends Seeder
{
    /**
     * The 9 given goal plans from the diet page.
     * Daily targets are the middle of each plan's range (same numbers the admin page uses).
     */
    public function run(): void
    {
        $presets = [
            [
                'slug' => 'muscle-gain',
                'name' => 'Muscle Gain Plan',
                'description' => 'High protein and calorie surplus to support muscle growth.',
                'calories' => 3000,
                'protein' => 190,
                'carbs' => 375,
                'fats' => 90,
                'stats' => [
                    'Calories' => '2800-3200',
                    'Protein' => '180-200g',
                    'Carbs' => '350-400g',
                    'Fats' => '80-100g',
                ],
                'sample_meals' => [
                    '🍳 Breakfast: Oatmeal with protein powder, banana, almonds',
                    '🍎 Snack: Greek yogurt with berries',
                    '🥗 Lunch: Grilled chicken, brown rice, vegetables',
                    '🥤 Snack: Protein shake, apple',
                    '🍽 Dinner: Salmon, sweet potato, broccoli',
                    '🌙 Before Bed: Cottage cheese',
                ],
            ],
            [
                'slug' => 'weight-loss',
                'name' => 'Weight Loss Plan',
                'description' => 'Lower calories to help burn fat while maintaining nutrients.',
                'calories' => 1650,
                'protein' => 135,
                'carbs' => 165,
                'fats' => 55,
                'stats' => [
                    'Calories' => '1500-1800',
                    'Protein' => '120-150g',
                    'Carbs' => '150-180g',
                    'Fats' => '50-60g',
                ],
                'sample_meals' => [
                    '🍳 Breakfast: Egg whites, spinach, whole grain toast',
                    '🍎 Snack: Apple with almond butter',
                    '🥗 Lunch: Turkey breast, quinoa, mixed greens',
                    '🥤 Snack: Protein smoothie',
                    '🍽 Dinner: Grilled fish, asparagus, cauliflower rice',
                    '🌙 Before Bed: Casein protein shake',
                ],
            ],
            [
                'slug' => 'balanced',
                'name' => 'Balanced Nutrition',
                'description' => 'Balanced macros to maintain a healthy body weight.',
                'calories' => 2200,
                'protein' => 150,
                'carbs' => 265,
                'fats' => 70,
                'stats' => [
                    'Calories' => '2000-2400',
                    'Protein' => '140-160g',
                    'Carbs' => '250-280g',
                    'Fats' => '65-75g',
                ],
                'sample_meals' => [
                    '🍳 Breakfast: Whole grain pancakes, eggs, berries',
                    '🍎 Snack: Mixed nuts and dried fruit',
                    '🥗 Lunch: Chicken stir-fry, brown rice, vegetables',
                    '🥤 Snack: Hummus with veggie sticks',
                    '🍽 Dinner: Lean beef, baked potato, green beans',
                    '🌙 Before Bed: Greek yogurt',
                ],
            ],
            [
                'slug' => 'performance',
                'name' => 'Performance Plan',
                'description' => 'Higher carbs and protein for energy, endurance, and recovery.',
                'calories' => 2600,
                'protein' => 150,
                'carbs' => 325,
                'fats' => 80,
                'stats' => [
                    'Calories' => '2400-2800',
                    'Protein' => '140-160g',
                    'Carbs' => '300-350g',
                    'Fats' => '70-90g',
                ],
                'sample_meals' => [
                    '🍳 Breakfast: Eggs, toast, and fruit',
                    '🍎 Snack: Protein bar',
                    '🥗 Lunch: Chicken breast with rice and vegetables',
                    '🥤 Snack: Protein shake',
                    '🍽 Dinner: Salmon with sweet potatoes',
                    '🌙 Before Bed: Greek yogurt',
                ],
            ],
            [
                'slug' => 'high-protein',
                'name' => 'High Protein Plan',
                'description' => 'Extra protein intake to support muscle repair and strength development.',
                'calories' => 2350,
                'protein' => 200,
                'carbs' => 200,
                'fats' => 70,
                'stats' => [
                    'Calories' => '2200-2500',
                    'Protein' => '180-220g',
                    'Carbs' => '180-220g',
                    'Fats' => '60-80g',
                ],
                'sample_meals' => [
                    '🍳 Breakfast: Eggs, oats, protein shake',
                    '🍎 Snack: Peanut butter toast',
                    '🥗 Lunch: Chicken breast, brown rice, broccoli',
                    '🥤 Snack: Protein smoothie',
                    '🍽 Dinner: Lean steak, sweet potato',
                    '🌙 Before Bed: Greek yogurt',
                ],
            ],
            [
                'slug' => 'fasting',
                'name' => 'Fasting Plan',
                'description' => 'Timed eating windows that help improve metabolism and fat burning.',
                'calories' => 1900,
                'protein' => 140,
                'carbs' => 165,
                'fats' => 65,
                'stats' => [
                    'Calories' => '1800-2000',
                    'Protein' => '130-150g',
                    'Carbs' => '150-180g',
                    'Fats' => '60-70g',
                ],
                'sample_meals' => [
                    '🍳 Breakfast: Eggs, avocado, whole grain toast',
                    '🍎 Snack: Protein shake',
                    '🥗 Lunch: Grilled chicken, quinoa, vegetables',
                    '🥤 Snack: Greek yogurt with berries',
                    '🍽 Dinner: Salmon with brown rice and broccoli',
                ],
            ],
            [
                'slug' => 'keto',
                'name' => 'Keto Plan',
                'description' => 'Low-carb, high-fat eating to shift your body into a fat-burning ketosis state.',
                'calories' => 2000,
                'protein' => 130,
                'carbs' => 35,
                'fats' => 145,
                'stats' => [
                    'Calories' => '1800-2200',
                    'Protein' => '120-140g',
                    'Carbs' => '20-50g',
                    'Fats' => '130-160g',
                ],
                'sample_meals' => [
                    '🍳 Breakfast: Scrambled eggs with butter, bacon, avocado',
                    '🍎 Snack: Almonds and string cheese',
                    '🥗 Lunch: Tuna salad with olive oil over leafy greens',
                    '🥤 Snack: Boiled eggs with salt',
                    '🍽 Dinner: Ribeye steak, roasted zucchini, garlic butter',
                    '🌙 Before Bed: Peanut butter with celery sticks',
                ],
            ],
            [
                'slug' => 'plant-based',
                'name' => 'Plant-Based Plan',
                'description' => 'Whole-food, plant-powered meals that fuel performance and support overall wellness.',
                'calories' => 2100,
                'protein' => 115,
                'carbs' => 300,
                'fats' => 63,
                'stats' => [
                    'Calories' => '1900-2300',
                    'Protein' => '100-130g',
                    'Carbs' => '280-320g',
                    'Fats' => '55-70g',
                ],
                'sample_meals' => [
                    '🍳 Breakfast: Oatmeal with chia seeds, berries, and almond butter',
                    '🍎 Snack: Edamame with sea salt',
                    '🥗 Lunch: Lentil and quinoa bowl with roasted veggies',
                    '🥤 Snack: Smoothie with spinach, banana, plant protein',
                    '🍽 Dinner: Tofu stir-fry with brown rice and broccoli',
                    '🌙 Before Bed: Soy milk with a handful of walnuts',
                ],
            ],
            [
                'slug' => 'cut-lean',
                'name' => 'Cut & Lean Plan',
                'description' => 'Strategic calorie cycling to shed body fat while preserving hard-earned lean muscle.',
                'calories' => 1800,
                'protein' => 175,
                'carbs' => 140,
                'fats' => 53,
                'stats' => [
                    'Calories' => '1600-2000',
                    'Protein' => '160-190g',
                    'Carbs' => '120-160g',
                    'Fats' => '45-60g',
                ],
                'sample_meals' => [
                    '🍳 Breakfast: Egg white omelette, spinach, whole grain toast',
                    '🍎 Snack: Rice cakes with cottage cheese',
                    '🥗 Lunch: Grilled turkey, mixed greens, light vinaigrette',
                    '🥤 Snack: Whey protein shake with water',
                    '🍽 Dinner: Tilapia, steamed asparagus, cauliflower mash',
                    '🌙 Before Bed: Casein protein shake',
                ],
            ],
        ];

        foreach ($presets as $preset) {
            GoalPreset::updateOrCreate(
                ['slug' => $preset['slug']],
                collect($preset)->except('slug')->all()
            );
        }
    }
}
