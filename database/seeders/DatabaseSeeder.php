<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $user = new User();

        $user->name = 'packvault';
        $user->email = 'admin@packvault.com';
        $user->password = 'packvault';
        $user->email_verified_at = now();
        $user->save();
    }
}
