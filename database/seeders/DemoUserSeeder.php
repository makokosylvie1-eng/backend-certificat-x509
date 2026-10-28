<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'demo@unikin.test'],
            ['name' => 'Sylvie Makoko', 'password' => 'Demo2026pass']
        );
    }
}