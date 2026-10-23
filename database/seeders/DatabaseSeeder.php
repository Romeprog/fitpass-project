<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     * Safe to run more than once (php artisan db:seed).
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Only create the test user if it does not exist yet (a second run used to fail on the duplicate email)
        if (! User::where('email', 'test@example.com')->exists()) {
            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
        }

        $this->call([
            AdminSeeder::class,
            GoalPresetSeeder::class,
            WorkoutSeeder::class,
            FoodSeeder::class,
        ]);
    }
}
