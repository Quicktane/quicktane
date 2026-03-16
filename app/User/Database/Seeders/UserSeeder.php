<?php

declare(strict_types=1);

namespace App\User\Database\Seeders;

use App\User\Models\Role;
use App\User\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $superAdminRole = Role::where('slug', 'super-admin')->first();

        User::firstOrCreate(
            ['email' => 'admin@quicktane.local'],
            [
                'first_name' => 'Admin',
                'last_name' => 'User',
                'email' => 'admin@quicktane.local',
                'password' => Hash::make('password'),
                'role_id' => $superAdminRole?->id,
                'is_active' => true,
            ],
        );
    }
}
