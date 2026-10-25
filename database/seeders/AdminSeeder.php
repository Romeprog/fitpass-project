<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrNew(['email' => 'fitpass.admin@gmail.com']);
        $admin->name = 'Admin';
        $admin->password = Hash::make('fitpass_admin123');
        $admin->role = 'admin';
        $admin->save();
    }
}