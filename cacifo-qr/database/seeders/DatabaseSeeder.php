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
     */
     public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Administrador',
                'password' => bcrypt('123'),
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'user1@test.com'],
            [
                'name' => 'Utilizador 1',
                'password' => bcrypt('123'),
                'role' => 'user',
            ]
        );

        User::updateOrCreate(
            ['email' => 'user2@test.com'],
            [
                'name' => 'Utilizador 2',
                'password' => bcrypt('123'),
                'role' => 'user',
            ]
        );

        $this->call(LockerSeeder::class);
    }
}
