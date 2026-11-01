<?php

namespace Database\Seeders;

use App\Models\ClassRoom;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['username' => 'superadmin'],
            [
                'name' => 'SuperAdmin SIPENA',
                'email' => 'admin@sipena.test',
                'password' => Hash::make('password'),
                'role' => 'superadmin',
                'class_id' => null,
            ]
        );

        ClassRoom::query()->orderBy('name')->get()->each(function (ClassRoom $class) {
            $username = strtolower(str_replace(['.', '-'], '', $class->name)); // X.1 → x1

            User::updateOrCreate(
                ['username' => $username],
                [
                    'name' => "Perwakilan {$class->name}",
                    'email' => null,
                    'password' => Hash::make('password'),
                    'role' => 'user',
                    'class_id' => $class->id,
                ]
            );
        });
    }
}
