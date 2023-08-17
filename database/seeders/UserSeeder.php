<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;


class UserSeeder extends Seeder
{
    public function run(): void
    {
        $user_list = Permission::create(['name' => 'users.list']);
        $user_create = Permission::create(['name' => 'users.create']);
        $user_destroy = Permission::create(['name' => 'users.destroy']);
        $user_edit = Permission::create(['name' => 'users.edit']);
        $user_update = Permission::create(['name' => 'users.update']);
        $user_view = Permission::create(['name' => 'users.view']);

        $admin_role = Role::create(['name' => 'admin']);
        $admin_role->givePermissionTo([
            $user_list,
            $user_create,
            $user_edit,
            $user_destroy,
            $user_update,
            $user_view
        ]);

        $admin = User::create([
            'name' => 'EDGAR STIBEN MADRID LONDOÑO',
            'email' => 'stivenmadrid6@gmail.com',
            'password' => bcrypt('password')
        ]);

        $admin->assignRole($admin_role); // Asignar el rol al usuario
        $admin->givePermissionTo([
            $user_list,
            $user_create,
            $user_edit,
            $user_destroy,
            $user_update,
            $user_view
        ]);

       
    }
}
