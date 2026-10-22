<?php

namespace Database\Seeders;

use App\Models\Food;
use Illuminate\Database\Seeder;

class FoodSeeder extends Seeder
{
    /**
     * The 22 given foods (user_id = null) for quick-add on the diet page.
     * Each row: name, calories, protein, carbs, fats (per serving).
     */
    public function run(): void
    {
        $foods = [
            ['Egg (1 large)', 78, 6, 0.6, 5],
            ['Oatmeal (1 cup cooked)', 158, 6, 27, 3],
            ['Whole grain bread (1 slice)', 80, 4, 14, 1],
            ['Banana (1 medium)', 105, 1.3, 27, 0.4],
            ['Apple (1 medium)', 95, 0.5, 25, 0.3],
            ['Greek yogurt (170 g)', 100, 17, 6, 0.7],
            ['Cottage cheese (1 cup)', 220, 25, 8, 10],
            ['Whey protein shake (1 scoop)', 120, 24, 3, 1.5],
            ['Chicken breast (100 g, grilled)', 165, 31, 0, 3.6],
            ['Lean steak (100 g)', 180, 27, 0, 7],
            ['Salmon (100 g)', 208, 20, 0, 13],
            ['Tuna, canned in water (100 g)', 116, 26, 0, 1],
            ['Tofu (100 g)', 76, 8, 1.9, 4.8],
            ['White rice (1 cup cooked)', 205, 4.3, 45, 0.4],
            ['Brown rice (1 cup cooked)', 216, 5, 45, 1.8],
            ['Pasta (1 cup cooked)', 220, 8, 43, 1.3],
            ['Sweet potato (1 medium)', 103, 2.3, 24, 0.2],
            ['Lentils (1 cup cooked)', 230, 18, 40, 0.8],
            ['Broccoli (1 cup)', 55, 3.7, 11, 0.6],
            ['Avocado (½ fruit)', 120, 1.5, 6, 11],
            ['Almonds (28 g)', 164, 6, 6, 14],
            ['Peanut butter (1 tbsp)', 94, 4, 3, 8],
        ];

        foreach ($foods as [$name, $calories, $protein, $carbs, $fats]) {
            Food::updateOrCreate(
                ['user_id' => null, 'name' => $name],
                ['calories' => $calories, 'protein' => $protein, 'carbs' => $carbs, 'fats' => $fats]
            );
        }
    }
}
