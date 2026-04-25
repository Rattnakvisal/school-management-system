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
            ['email' => 'rattnakvisalchun@gmail.com'],
            [
                'name' => 'Chun Rattnakvisal',
                'phone_number' => '078841050',
                'password' => 'Wq_76wZtR2aPRmq',
                'role' => 'admin',
                'email_verified_at' => now(),
                'is_active' => true,
            ],
            ['email' => 'visalchunrathanak@gmail.com'],
            [
                'name' => 'Chun Rattnakvisal',
                'phone_number' => '095300551',
                'password' => 'Wq_76wZtR2aPRmq',
                'role' => 'staff',
                'email_verified_at' => now(),
                'is_active' => true,
            ],
            ['email' => 'chunrattnakvisal246@gmail.com'],
            [
                'name' => 'Chun Rattnakvisal',
                'phone_number' => '0762223238',
                'password' => 'Wq_76wZtR2aPRmq',
                'role' => 'admin',
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );
    }
}
