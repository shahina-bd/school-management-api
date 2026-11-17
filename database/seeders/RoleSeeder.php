<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            'Super Admin' => 'System Super Administrator with full access',
            'Admin' => 'Administrator with management access',
            'Accountant' => 'Financial and accounting manager',
            'Teacher' => 'Teaching and academic staff',
            'Staff' => 'General school staff member',
            'Student' => 'Enrolled student',
            'Guardian' => 'Parent or legal guardian of a student',
        ];

        foreach ($roles as $name => $description) {
            Role::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'description' => $description,
                    'is_active' => true,
                ]
            );
        }
    }
}
