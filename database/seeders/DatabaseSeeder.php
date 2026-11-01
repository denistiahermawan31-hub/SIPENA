<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(ClassSeeder::class);
        $this->call(StudentSeeder::class);
        $this->call(UserSeeder::class);

        Setting::put('tadarus_weight', (string) config('sipena.scores.tadarus.ikut', 2));

        $this->call(DailyReportSeeder::class);
    }
}
