<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_belong_to_a_role_via_role_id(): void
    {
        $role = Role::create([
            'name' => 'Teacher',
            'slug' => 'teacher',
        ]);

        $user = User::factory()->create([
            'role_id' => $role->id,
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'role_id' => $role->id,
        ]);

        $this->assertTrue($user->role->is($role));
    }
}
