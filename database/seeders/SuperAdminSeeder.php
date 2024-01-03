<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Role::create(['name' => 'super-admin', 'guard_name' => 'api']);
        $permission = Permission::create(['name' => 'update-passwords', 'guard_name' => 'api']);
        $permissions = Permission::all();
        $role->syncPermissions($permissions);
        $user = \App\Models\User::find(1);
        $user->assignRole($role);
    }
}
