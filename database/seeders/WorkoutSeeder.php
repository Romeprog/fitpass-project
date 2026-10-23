<?php

namespace Database\Seeders;

use App\Models\Exercise;
use App\Models\Workout;
use Illuminate\Database\Seeder;

class WorkoutSeeder extends Seeder
{
    /**
     * The 9 given workouts (63 exercises) from the workouts page.
     * The slug is the short id the site already uses, e.g. /workouts#arm.
     */
    public function run(): void
    {
        $workouts = [
            [
                'slug' => 'arm',
                'name' => 'Arm Workout',
                'category' => 'strength',
                'level' => 'Intermediate',
                'minutes' => 25,
                'description' => 'Build strength and definition in your biceps, triceps, and forearms.',
                'exercises' => [
                    ['Diamond Push Up', '10 Reps • 3 Sets'],
                    ['Push Up', '10 Reps • 3 Sets'],
                    ['Push Up & Rotation', '12 Reps • 2 Sets'],
                    ['Burpees', '16 Reps • 2 Sets'],
                    ['Tricep Dips', '12 Reps • 3 Sets'],
                    ['Triceps Push Up', '14 Reps • 2 Sets'],
                    ['Plank Shoulder Taps', '12 Reps • 3 Sets'],
                ],
            ],
            [
                'slug' => 'chest',
                'name' => 'Chest Workout',
                'category' => 'strength',
                'level' => 'Intermediate',
                'minutes' => 25,
                'description' => 'Strengthen and sculpt your pectoral muscles with targeted push variations.',
                'exercises' => [
                    ['Push Up', '10 Reps • 3 Sets'],
                    ['Wide Push Up', '12 Reps • 2 Sets'],
                    ['Diamond Push Up', '12 Reps • 3 Sets'],
                    ['Decline Push Up', '10 Reps • 2 Sets'],
                    ['Incline Push Up', '10 Reps • 2 Sets'],
                    ['Archer Push Up', '10 Reps • 2 Sets'],
                    ['Bench Press', '10 Reps • 2 Sets'],
                ],
            ],
            [
                'slug' => 'leg',
                'name' => 'Leg Workout',
                'category' => 'strength',
                'level' => 'Intermediate',
                'minutes' => 30,
                'description' => 'Power up your quads, hamstrings, and glutes with lower body exercises.',
                'exercises' => [
                    ['Squat', '12 Reps • 2 Sets'],
                    ['Reverse Lunges', '12 Reps • 3 Sets'],
                    ['Step Up', '15 Reps • 3 Sets'],
                    ['Curtsy Lunges', '14 Reps • 2 Sets'],
                    ['Lunges', '10 Reps • 2 Sets'],
                    ['Bulgarian Split Squat', '10 Reps • 3 Sets'],
                    ['Jump Squats', '14 Reps • 2 Sets'],
                ],
            ],
            [
                'slug' => 'shoulder',
                'name' => 'Shoulder Workout',
                'category' => 'strength',
                'level' => 'Intermediate',
                'minutes' => 25,
                'description' => 'Develop strong, defined shoulders with focused deltoid exercises.',
                'exercises' => [
                    ['Lateral Raise', '10 Reps • 3 Sets'],
                    ['Pike Push Up', '12 Reps • 3 Sets'],
                    ['Decline Push Up', '10 Reps • 3 Sets'],
                    ['Incline Push Up', '10 Reps • 3 Sets'],
                    ['Plank To Downward Dog', '12 Reps • 3 Sets'],
                    ['Shoulder Taps', '20 Reps • 3 Sets'],
                    ['Tricep Dips', '12 Reps • 3 Sets'],
                ],
            ],
            [
                'slug' => 'abs',
                'name' => 'Abs Workout',
                'category' => 'core',
                'level' => 'Beginner',
                'minutes' => 20,
                'description' => 'Tone and tighten your core with effective abdominal exercises.',
                'exercises' => [
                    ['Sit Up', '13 Reps • 3 Sets'],
                    ['Leg Raise', '12 Reps • 3 Sets'],
                    ['Plank', '2:00 minutes • 2 Sets'],
                    ['Russian Twist', '10 Reps • 3 Sets'],
                    ['Bicycle Crunches', '14 Reps • 2 Sets'],
                    ['Heel Touch', '20 Reps • 3 Sets'],
                    ['V-Up', '16 Reps • 2 Sets'],
                ],
            ],
            [
                'slug' => 'back',
                'name' => 'Back Workout',
                'category' => 'strength',
                'level' => 'Advanced',
                'minutes' => 30,
                'description' => 'Build a wider, stronger back with pull-focused and posterior chain movements.',
                'exercises' => [
                    ['Pull Ups', '12 Reps • 3 Sets'],
                    ['Australian Pull-Ups', '14 Reps • 2 Sets'],
                    ['Reverse Snow Angels', '15 Reps • 3 Sets'],
                    ['Back Widow', '12 Reps • 3 Sets'],
                    ['Scapula Push Up', '10 Reps • 2 Sets'],
                    ['Superman Hold', '50 seconds • 2 Sets'],
                    ['Resistance Band Deadlift', '10 Reps • 3 Sets'],
                ],
            ],
            [
                'slug' => 'cardio',
                'name' => 'Cardio Workout',
                'category' => 'cardio',
                'level' => 'Beginner',
                'minutes' => 25,
                'description' => 'Boost your heart rate and burn calories with energetic cardio exercises.',
                'exercises' => [
                    ['Jumping Jacks', '45 seconds • 3 Sets'],
                    ['High Knees', '40 seconds • 3 Sets'],
                    ['Jump Rope', '60 seconds • 3 Sets'],
                    ['Butt Kicks', '40 seconds • 3 Sets'],
                    ['Mountain Climbers', '30 seconds • 3 Sets'],
                    ['Box Step-Ups', '15 Reps • 3 Sets'],
                    ['Running in Place', '60 seconds • 2 Sets'],
                ],
            ],
            [
                'slug' => 'fullbody',
                'name' => 'Full Body Workout',
                'category' => 'full',
                'level' => 'Intermediate',
                'minutes' => 35,
                'description' => 'Hit every muscle group with a complete full-body training session.',
                'exercises' => [
                    ['Burpees', '12 Reps • 3 Sets'],
                    ['Squat to Press', '12 Reps • 3 Sets'],
                    ['Push Up to Row', '10 Reps • 3 Sets'],
                    ['Reverse Lunge with Twist', '12 Reps • 2 Sets'],
                    ['Plank Up-Down', '10 Reps • 3 Sets'],
                    ['Glute Bridge', '15 Reps • 3 Sets'],
                    ['Dead Bug', '10 Reps • 2 Sets'],
                ],
            ],
            [
                'slug' => 'hiit',
                'name' => 'HIIT Workout',
                'category' => 'cardio',
                'level' => 'Advanced',
                'minutes' => 20,
                'description' => 'Max-intensity intervals to torch fat and skyrocket your metabolism.',
                'exercises' => [
                    ['Burpees', '20s on • 10s rest • 4 Sets'],
                    ['Jump Squats', '20s on • 10s rest • 4 Sets'],
                    ['Explosive Push Ups', '20s on • 10s rest • 3 Sets'],
                    ['Sprint in Place', '20s on • 10s rest • 4 Sets'],
                    ['Plank to Push Up', '20s on • 10s rest • 3 Sets'],
                    ['Lateral Bounds', '20s on • 10s rest • 3 Sets'],
                    ['Tuck Jumps', '20s on • 10s rest • 4 Sets'],
                ],
            ],
        ];

        foreach ($workouts as $data) {
            $workout = Workout::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'name' => $data['name'],
                    'category' => $data['category'],
                    'level' => $data['level'],
                    'minutes' => $data['minutes'],
                    'description' => $data['description'],
                ]
            );

            foreach ($data['exercises'] as $i => [$name, $detail]) {
                Exercise::updateOrCreate(
                    ['workout_id' => $workout->id, 'sort_order' => $i + 1],
                    ['name' => $name, 'detail' => $detail]
                );
            }
        }
    }
}
