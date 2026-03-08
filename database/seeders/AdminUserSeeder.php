<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Admin Supremo',
            'email' => 'admin@PagueMax.com',
            'password' => Hash::make('password'),
            'is_admin' => true, // ou 'role' => 'admin', dependendo da sua migration
        ]);
    }
}