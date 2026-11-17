<?php

namespace Tests\Feature;

use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RoleSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_initial_roles_are_seeded(): void
    {
        $this->seed(RoleSeeder::class);

        $expectedRoles = [
            'Super Admin',
            'Admin',
            'Accountant',
            'Teacher',
            'Staff',
            'Student',
            'Guardian',
        ];

        foreach ($expectedRoles as $roleName) {
            $this->assertDatabaseHas('roles', [
                'name' => $roleName,
                'is_active' => true,
            ]);
        }

        $this->assertEquals(7, DB::table('roles')->count());
    }
}
