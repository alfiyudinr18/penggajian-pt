<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // 🔹 Buat role admin jika belum ada
        $adminRole = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);

        // 🔹 Buat user admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@penggajian.test'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('admin123'), // ganti setelah login
            ]
        );

        // 🔹 Assign role admin
        if (!$admin->hasRole('admin')) {
            $admin->assignRole($adminRole);
        }
    }
}
