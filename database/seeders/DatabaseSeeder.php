<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        Permission::create(['name' => 'permission-list']);
        Permission::create(['name' => 'permission-create']);

        // create roles and assign existing permissions
        $role1 = Role::create(['name' => 'Admin']);
        $role2 = Role::create(['name' => 'Content Writer']);
        $role2 = Role::create(['name' => 'Mobile User']);

        $role1->givePermissionTo('permission-list');
        $role1->givePermissionTo('permission-create');

        // gets all permissions via Gate::before rule; see AuthServiceProvider

        // create demo users
        $user = \App\Models\User::create([
            'user_name' => 'Admin User',
            'email' => 'admin@gmail.com',
            'role_id' => 1,
            'password' => Hash::make('12345678'),

        ]);
        $user->assignRole($role1);
    }
}
