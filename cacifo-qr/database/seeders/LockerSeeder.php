<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Locker;


class LockerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Locker::updateOrCreate(
            ['id' => 1],
            [
                'name' => 'Cacifo 1',
                'location' => 'ESTG - Corredor Principal',
                'status' => 'available',
                'door_open' => false,
                'open_command' => false,
            ]
        );

        Locker::updateOrCreate(
            ['id' => 2],
            [
                'name' => 'Cacifo 2',
                'location' => 'ESTG - Corredor Principal',
                'status' => 'available',
                'door_open' => false,
                'open_command' => false,
            ]
        );
    }
}
