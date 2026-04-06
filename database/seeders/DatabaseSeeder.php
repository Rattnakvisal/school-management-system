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
        // User::factory(10)->create();

        User::query()->updateOrCreate(
            ['email' => 'visalchunrathanak@gmail.com'],
            [
                'name' => 'Chun Rattnak Visal',
                'phone_number' => '078841050',
                'password' => 'Wq_76wZtR2aPRmq',
                'role' => 'admin',
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );
    }
}
