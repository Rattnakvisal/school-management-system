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

        $users = [
            [
                'name' => 'Chun Rattnakvisal',
                'email' => 'rattnakvisalchun@gmail.com',
                'phone_number' => '078841050',
                'password' => 'Wq_76wZtR2aPRmq',
                'role' => 'admin',
            ],
            [
                'name' => 'Chun Rattnakvisal',
                'email' => 'visalchunrathanak@gmail.com',
                'phone_number' => '095300551',
                'password' => 'Wq_76wZtR2aPRmq',
                'role' => 'staff',
            ],
            [
                'name' => 'Chun Rattnakvisal',
                'email' => 'chunrattnakvisal246@gmail.com',
                'phone_number' => '0762223238',
                'password' => 'Wq_76wZtR2aPRmq',
                'role' => 'teacher',
            ],
        ];

        foreach ($users as $user) {
            User::query()->updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'phone_number' => $user['phone_number'],
                    'password' => $user['password'],
                    'role' => $user['role'],
                    'email_verified_at' => now(),
                    'is_active' => true,
                ]
            );
        }
    }
}
