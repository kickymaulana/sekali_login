<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat Permission & Role
        $roleAdmin = Role::create(['name' => 'admin']);
        $roleUser = Role::create(['name' => 'user']);

        $permissionManageUsers = Permission::create(['name' => 'manage users']);
        $roleAdmin->givePermissionTo($permissionManageUsers);

        // Buat User Admin Testing
        $user = User::create([
            'name' => 'Kicky Maulana',
            'email' => 'kickymaulana@gmail.com',
            'password' => bcrypt('password123'),
        ]);

        $user->assignRole($roleAdmin);
    }
}
