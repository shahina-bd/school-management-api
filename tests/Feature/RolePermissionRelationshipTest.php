<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolePermissionRelationshipTest extends TestCase
{
    use RefreshDatabase;

    public function test_role_belongs_to_many_permissions(): void
    {
        $role = Role::create([
            'name' => 'Admin',
            'slug' => 'admin',
        ]);

        $permission1 = Permission::create([
            'name' => 'View Users',
            'slug' => 'view-users',
            'module' => 'User Management',
        ]);

        $permission2 = Permission::create([
            'name' => 'Delete Users',
            'slug' => 'delete-users',
            'module' => 'User Management',
        ]);

        $role->permissions()->attach([$permission1->id, $permission2->id]);

        $this->assertCount(2, $role->permissions);
        $this->assertTrue($role->permissions->contains($permission1));
        $this->assertTrue($role->permissions->contains($permission2));
    }

    public function test_permission_belongs_to_many_roles(): void
    {
        $permission = Permission::create([
            'name' => 'Manage Fees',
            'slug' => 'manage-fees',
            'module' => 'Fees',
        ]);

        $role1 = Role::create([
            'name' => 'Accountant',
            'slug' => 'accountant',
        ]);

        $role2 = Role::create([
            'name' => 'Super Admin',
            'slug' => 'super-admin',
        ]);

        $permission->roles()->attach([$role1->id, $role2->id]);

        $this->assertCount(2, $permission->roles);
        $this->assertTrue($permission->roles->contains($role1));
        $this->assertTrue($permission->roles->contains($role2));
    }
}
