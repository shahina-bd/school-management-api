<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'admin@school.com'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('password'),
            ]
        );

        $superAdminRole = Role::where('slug', 'super-admin')->first();
        if ($superAdminRole) {
            $user->roles()->syncWithoutDetaching([$superAdminRole->id]);
        }
    }
}
