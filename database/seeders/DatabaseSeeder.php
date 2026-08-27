<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            SchoolLevelSeeder::class,
            SchoolSeeder::class,
            RegistrationTrackSeeder::class,
            MajorSeeder::class,
            SettingSeeder::class,
            DemoDataSeeder::class,
        ]);
    }
}
