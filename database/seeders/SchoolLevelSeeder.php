<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SchoolLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $levels = [
            ['name' => 'TK', 'description' => 'Taman Kanak-kanak', 'is_active' => true],
            ['name' => 'SD', 'description' => 'Sekolah Dasar', 'is_active' => true],
            ['name' => 'SMP', 'description' => 'Sekolah Menengah Pertama', 'is_active' => true],
            ['name' => 'SMA', 'description' => 'Sekolah Menengah Atas', 'is_active' => true],
            ['name' => 'SMK', 'description' => 'Sekolah Menengah Kejuruan', 'is_active' => true],
        ];

        foreach ($levels as $level) {
            \DB::table('school_levels')->updateOrInsert(
                ['name' => $level['name']],
                [
                    'description' => $level['description'],
                    'is_active' => $level['is_active'],
                    'updated_at' => now(),
                ]
            );
        }
    }
}
