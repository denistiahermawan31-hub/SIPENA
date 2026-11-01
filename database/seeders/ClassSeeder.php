<?php

namespace Database\Seeders;

use App\Models\ClassRoom;
use Illuminate\Database\Seeder;

class ClassSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['name' => 'X.1', 'level' => 'X'],
            ['name' => 'X.2', 'level' => 'X'],
            ['name' => 'X.3', 'level' => 'X'],
            ['name' => 'X.4', 'level' => 'X'],
            ['name' => 'XI.1', 'level' => 'XI'],
            ['name' => 'XI.2', 'level' => 'XI'],
            ['name' => 'XI.3', 'level' => 'XI'],
            ['name' => 'XII.1', 'level' => 'XII'],
            ['name' => 'XII.2', 'level' => 'XII'],
            ['name' => 'XII.3', 'level' => 'XII'],
        ] as $specs) {
            ClassRoom::updateOrCreate(['name' => $specs['name']], ['level' => $specs['level']]);
        }
    }
}
