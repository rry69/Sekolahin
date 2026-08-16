<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SchoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $schools = [
            [
                'name' => 'SMK Negeri 1 Jakarta',
                'address' => 'Jl. Budi Utomo No.7, Jakarta Pusat 10710',
                'phone' => '021-3456789',
                'email' => 'info@smkn1jakarta.sch.id',
                'principal_name' => 'Dr. Budi Santoso, M.Pd',
            ],
        ];

        foreach ($schools as $school) {
            $id = \DB::table('schools')->insertGetId(array_merge($school, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));

            // Sekolah SMK melayani jenjang SMK (level id 5).
            $smkLevel = \DB::table('school_levels')->where('name', 'SMK')->value('id');

            if ($smkLevel) {
                \DB::table('school_level_school')->insert([
                    'school_id' => $id,
                    'school_level_id' => $smkLevel,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
